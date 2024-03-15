<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\project\controller;

use app\base\BaseController;
use app\project\model\ProjectTask as TaskList;
use app\oa\model\Schedule as ScheduleList;
use app\project\validate\TaskCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Task extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $param['uid'] = $this->uid;
            $list = (new TaskList())->list($param);
            return table_assign(0, '', $list);
        } else {
            View::assign('cate', work_cate());
            View::assign('project', get_project($this->uid));
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
            if (isset($param['end_time'])) {
                $param['end_time'] = strtotime(urldecode($param['end_time']));
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $task = (new TaskList())->detail($param['id']);
                try {
                    validate(TaskCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				if (isset($param['flow_status'])) {
					if ($param['flow_status'] == 3) {
						$param['over_time'] = time();
						$param['done_ratio'] = 100;
						if($task['before_task']>0){
							$flow_status = Db::name('ProjectTask')->where(['id' => $task['before_task']])->value('flow_status');
							if($flow_status !=3){
								return to_assign(1, '前置任务未完成，不能设置已完成');
							}
						}
					} else {
						$param['over_time'] = 0;
						$param['done_ratio'] = 0;
					}
				}
				if(isset($param['before_task'])){
					$after_task_array = admin_after_task_son($param['id']);
					//包括自己在内
					$after_task_array[] = $param['id'];
					if (in_array($param['before_task'], $after_task_array)) {
						return to_assign(1, '前置任务不能是该任务本身或其后置任务');
					}
				}
                $param['update_time'] = time();
                $res = TaskList::where('id', $param['id'])->strict(false)->save($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
					add_project_log($this->uid,'task',$param, $task);
                }
                return to_assign();
            } else {
                try {
                    validate(TaskCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $sid = TaskList::strict(false)->field(true)->insertGetId($param);
                if ($sid) {
                    add_log('add', $sid, $param);
                    $log_data = array(
                        'module' => 'task',
                        'task_id' => $sid,
                        'new_content' => $param['title'],
                        'field' => 'new',
                        'action' => 'add',
                        'admin_id' => $this->uid,
                        'create_time' => time(),
                    );
                    Db::name('ProjectLog')->strict(false)->field(true)->insert($log_data);
					//发消息
                    //$users = $param['director_uid'];
                    //sendMessage($users, 21, ['title' => $param['title'],'from_uid' => $this->uid, 'create_time'=>date('Y-m-d H:i:s',time()), 'action_id' => $sid]);
                }
                return to_assign();
            }
        } else {
            if (isset($param['project_id'])) {
                View::assign('project_id', $param['project_id']);
            }
            View::assign('cate', work_cate());
            return view();
        }
    }

    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = (new TaskList())->detail($id);
        if (empty($detail)) {
			echo '<div style="text-align:center;color:red;margin-top:20%;">该任务不存在</div>';exit;
        } else {
            $role_uid = [$detail['admin_id'], $detail['director_uid']];
            $role_edit = 'view';
            if (in_array($this->uid, $role_uid)) {
                $role_edit = 'edit';
            }
            $project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
			$auth = isAuth($this->uid,'project_admin');
            if (in_array($detail['project_id'], $project_ids) || in_array($this->uid, $role_uid) || in_array($this->uid, explode(",",$detail['assist_admin_ids'])) || ($auth==1&&$detail['project_id']>0) || ($this->uid==1)) {
                $file_array = Db::name('ProjectFile')
                ->field('mf.id,mf.topic_id,mf.admin_id,f.name,f.filesize,f.filepath,f.fileext,f.create_time,f.admin_id,a.name as admin_name')
                ->alias('mf')
                ->join('File f', 'mf.file_id = f.id', 'LEFT')
                ->join('Admin a', 'mf.admin_id = a.id', 'LEFT')
                ->order('mf.create_time desc')
                ->where(array('mf.topic_id' => $id, 'mf.module' => 'task'))
                ->select()->toArray();
							
				$son_task = Db::name('ProjectTask')->where(['pid' => $detail['id']])->select()->toArray();				
				foreach ($son_task as $key => &$vo) {
					$vo['flow_name'] = TaskList::$FlowStatus[(int) $vo['flow_status']];
				}

                View::assign('son_task', $son_task);
                View::assign('detail', $detail);
                View::assign('file_array', $file_array);
                View::assign('role_edit', $role_edit);
                View::assign('id', $id);
                return view();
            }
            else{
				echo '<div style="text-align:center;color:red;margin-top:20%;">您没权限查看该任务</div>';exit;
            }
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $detail = Db::name('ProjectTask')->where('id', $id)->find();
            if ($detail['admin_id'] != $this->uid) {
                return to_assign(1, "你不是该任务的创建人，无权限删除");
            }
			$count_schedule = Db::name('Schedule')->where(['tid'=>$id,'delete_time'=>0])->count();
			if($count_schedule>0){
				return to_assign(1, "该任务已经关联的工作记录，无法删除，如果不需要可以关闭该任务即可");
			}
            if (Db::name('ProjectTask')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                $log_data = array(
                    'module' => 'task',
                    'field' => 'delete',
                    'action' => 'delete',
                    'task_id' => $detail['id'],
                    'admin_id' => $this->uid,
                    'old_content' => '',
                    'new_content' => $detail['title'],
                    'create_time' => time(),
                );
                Db::name('ProjectLog')->strict(false)->field(true)->insert($log_data);
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	public function task_time() {
        if (request()->isAjax()) {
            $param = get_params();
            $tid = isset($param['tid']) ? $param['tid'] : 0;
            $where = [];
			if ($tid>0) {
                $task_ids = Db::name('ProjectTask')->where(['delete_time' => 0, 'project_id' => $param['tid']])->column('id');
				$where[] = ['a.tid', 'in', $task_ids];
            }
			else{
				if (!empty($param['uid'])) {
					$where[] = ['a.admin_id', '=', $param['uid']];
				} else {
					$where[] = ['a.admin_id', '=', $this->uid];
				}
				if (!empty($param['keywords'])) {
					$where[] = ['a.title', 'like', '%' . trim($param['keywords']) . '%'];
				}
				//按时间检索
				if (!empty($param['diff_time'])) {
					$diff_time =explode('~', $param['diff_time']);
					$where[] = ['a.start_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
				}
			}
            $where[] = ['a.delete_time', '=', 0];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $schedule = ScheduleList::where($where)
                ->field('a.*,u.name,d.title as department,w.title as work_cate')
				->alias('a')
				->join('Admin u', 'a.admin_id = u.id', 'LEFT')
				->join('Department d', 'u.did = d.id', 'LEFT')
				->join('WorkCate w', 'w.id = a.cid', 'LEFT')
				->order('a.end_time desc')
                ->paginate($rows, false)
                ->each(function ($item, $key) {
					$item->labor_type_string = '案头工作';
					if($item->labor_type == 2){
						$item->labor_type_string = '外勤工作';
					}
					if($item->tid > 0){
						$task = Db::name('ProjectTask')->where(['id' => $item->tid])->find();
						$item->task = $task['title'];
						$item->project = Db::name('Project')->where(['id' => $task['project_id']])->value('name');
					}
					$item->start_time_a = empty($item->start_time) ? '' : date('Y-m-d', $item->start_time);
					$item->start_time_b = empty($item->start_time) ? '' : date('H:i', $item->start_time);
					$item->end_time_a = empty($item->end_time) ? '' : date('Y-m-d', $item->end_time);
					$item->end_time_b = empty($item->end_time) ? '' : date('H:i', $item->end_time);
                    $item->start_time = empty($item->start_time) ? '' : date('Y-m-d H:i', $item->start_time);
                    //$item->end_time = empty($item->end_time) ? '': date('Y-m-d H:i', $item->end_time);
                    $item->end_time = empty($item->end_time) ? '' : date('H:i', $item->end_time);
                });
            return table_assign(0, '', $schedule);
        } else {
            return view();
        }
    }
}
