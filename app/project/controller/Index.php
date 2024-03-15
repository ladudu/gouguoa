<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\project\controller;

use app\base\BaseController;
use app\project\model\Project as ProjectList;
use app\project\validate\ProjectCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{	
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where = array();
			if (!empty($param['director_uid'])) {
				$where[] = ['director_uid', 'in', $param['director_uid']];
			}
			if (!empty($param['status'])) {
				$where[] = ['status', 'in', $param['status']];
			}
			if (!empty($param['keywords'])) {
				$where[] = ['name|content', 'like', '%' . $param['keywords'] . '%'];
			}			
			$where[] = ['delete_time', '=', 0];
			$auth = isAuth($this->uid,'project_admin');
			if($auth == 0){
				$project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
				$where[] = ['id', 'in', $project_ids];
			}	
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = ProjectList::withoutField('content,md_content')
                ->where($where)
                ->order('id desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->director_name = Db::name('Admin')->where(['id' => $item->director_uid])->value('name');
                    $item->plan_time = date('Y-m-d', $item->start_time) . ' 至 ' . date('Y-m-d', $item->end_time);
                    $item->status_name = ProjectList::$Status[(int) $item->status];

                    $task_map = [];
                    $task_map[] = ['project_id', '=', $item->id];
                    $task_map[] = ['delete_time', '=', 0];
                    //任务总数
                    $item->tasks_total = Db::name('ProjectTask')->where($task_map)->count();
                    //已完成任务
                    $task_map[] = ['flow_status', '>', 2]; //已完成
                    $item->tasks_finish = Db::name('ProjectTask')->where($task_map)->count();
                    //未完成任务
                    $item->tasks_unfinish = $item->tasks_total - $item->tasks_finish;
                    if ($item->tasks_total > 0) {
                        $item->tasks_pensent = round($item->tasks_finish / $item->tasks_total * 100, 2) . "％";
                    } else {
                        $item->tasks_pensent = "0％";
                    }
					
					$step = Db::name('Step')->where(['action_id' => $item->id,'sort'=>$item->step_sort,'type'=>2,'delete_time'=>0])->find();
					if(!empty($step)){
						$item->step_user = Db::name('Admin')->where(['id'=>$step['flow_uid']])->value('name');
						$item->step = $step['flow_name'] . '-' . $item->step_user;
					}
					else{						
						$item->step = '-';
					}
                });
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
            if (isset($param['start_time'])) {
                $param['start_time'] = strtotime(urldecode($param['start_time']));
            }
            if (isset($param['end_time'])) {
                $param['end_time'] = strtotime(urldecode($param['end_time']));
            }
			
			$param['status'] = 2;
			$param['step_sort'] = 0;
			$flowNameData = isset($param['flowName']) ? $param['flowName'] : '';
			$flowUidsData = isset($param['chargeIds']) ? $param['chargeIds'] : '';
			$flowIdsData = isset($param['membeIds']) ? $param['membeIds'] : '';
			$flowDateData = isset($param['cycleDate']) ? $param['cycleDate'] : '';
			$flow = [];
			$time_1 = $param['start_time'];
			$time_2 = $param['end_time'];
			foreach ($flowNameData as $key => $value) {
				if (!$value) {
					continue;
				}				
				$flowDate = explode('到',$flowDateData[$key]);
				$start_time = strtotime(urldecode(trim($flowDate[0])));
				$end_time = strtotime(urldecode(trim($flowDate[1])));
				if($start_time<$time_1){
					if($key == 0){
						return to_assign(1, '第'.($key+1).'阶段的开始时间不能小于计划开始时间');
					}
					else{
						return to_assign(1, '第'.($key+1).'阶段的开始时间不能小于第'.($key).'阶段的结束时间');
					}
					break;
				}
				if($end_time>$time_2){
					return to_assign(1, '第'.($key+1).'阶段的结束时间不能大于计划结束时间');
					break;
				}
				else{
					$time_1 = $end_time;
				}
				$item = [];
				$item['flow_name'] = $value;
				$item['type'] = 2;
				$item['flow_uid'] = $flowUidsData[$key];
				$item['flow_ids'] = $flowIdsData[$key];
				$item['sort'] = $key;
				$item['start_time'] = $start_time;
				$item['end_time'] = $end_time;
				$item['create_time'] = time();
				$flow[]=$item;	
			}
			//var_dump($flow);exit;			
			
			try {
				validate(ProjectCheck::class)->scene('add')->check($param);
			} catch (ValidateException $e) {
				// 验证失败 输出错误信息
				return to_assign(1, $e->getError());
			}
			$param['create_time'] = time();
			$param['admin_id'] = $this->uid;
			$sid = ProjectList::strict(false)->field(true)->insertGetId($param);
			if ($sid) {
				$project_users = $this->uid;
				if (!empty($param['director_uid'])){
					$project_users.=",".$param['director_uid'];
				}
				if (!empty($param['team_admin_ids'])){
					$project_users.=",".$param['team_admin_ids'];
				}
				$project_array = explode(",",(string)$project_users);
				$project_array = array_unique($project_array);
				$project_user_array=[];
				foreach ($project_array as $k => $v) {
					if (is_numeric($v)) {
						$project_user_array[]=array(
							'uid'=>$v,
							'admin_id'=>$this->uid,
							'project_id'=>$sid,
							'create_time'=>time(),
						);
					}
				}
				Db::name('ProjectUser')->strict(false)->field(true)->insertAll($project_user_array);
				
				//增加阶段
				foreach ($flow as $key => &$value) {
					$value['action_id'] = $sid;
				}
				Db::name('Step')->strict(false)->field(true)->insertAll($flow);		
		
				add_log('add', $sid, $param);
				$log_data = array(
					'module' => 'project',
					'project_id' => $sid,
					'new_content' => $param['name'],
					'field' => 'new',
					'action' => 'add',
					'admin_id' => $this->uid,
					'old_content' => '',
					'create_time' => time(),
				);
				Db::name('ProjectLog')->strict(false)->field(true)->insert($log_data);
			}
			return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = (new ProjectList())->detail($id);
                if (empty($detail)) {
                    return to_assign(1, '项目不存在');
                }
                View::assign('detail', $detail);
            }
			$section = ['立项阶段'];
			$section_conf = Db::name('DataAuth')->where('name','project_admin')->value('conf_1');
			if(!empty($section_conf)){
				$section = explode('|', $section_conf);
			}			
            View::assign('section', $section);
            View::assign('user_info', get_admin($this->uid));
            View::assign('id', $id);
            return view();
        }
    }

    //编辑
    public function edit()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
		$detail = (new ProjectList())->detail($id);
		if (request()->isPost()) {
			if ($this->uid == $detail['admin_id'] || $this->uid == $detail['director_uid']) {
				if (isset($param['start_time'])) {
					$param['start_time'] = strtotime(urldecode($param['start_time']));
					if ($param['start_time'] >= $detail['end_time']) {
						return to_assign(1, '开始时间不能大于计划结束时间');
					}
				}
				if (isset($param['end_time'])) {
					$param['end_time'] = strtotime(urldecode($param['end_time']));
					if ($param['end_time'] <= $detail['start_time']) {
						return to_assign(1, '计划结束时间不能小于开始时间');
					}
				}
				try {
					validate(ProjectCheck::class)->scene('edit')->check($param);
				} catch (ValidateException $e) {
					// 验证失败 输出错误信息
					return to_assign(1, $e->getError());
				}
				$param['update_time'] = time();
				$res = ProjectList::where('id', $param['id'])->strict(false)->field(true)->update($param);
				if ($res) {
					if(isset($param['director_uid'])){
						$project_user=array(
							'uid'=>$param['director_uid'],
							'admin_id'=>$this->uid,
							'project_id'=>$param['id'],
							'create_time'=>time(),
							'delete_time'=>0,
						);
						$has = Db::name('ProjectUser')->where(array('uid'=>$param['director_uid'],'project_id'=>$param['id']))->find();
						if(empty($has)){
							Db::name('ProjectUser')->strict(false)->field(true)->insert($project_user);
						}
						else{
							Db::name('ProjectUser')->where(array('id'=>$has['id']))->strict(false)->field(true)->update($project_user);						
						}
						
					}		
					
					add_log('edit', $param['id'], $param);
					add_project_log($this->uid,'project',$param, $detail);
				}
				return to_assign();
			} else {
				return to_assign(1, '只有创建人或者负责人才有权限编辑');
			}
		}
		else{
			if (empty($detail)) {
				return to_assign(1, '项目不存在');
			} else {
				//项目阶段			
				$step_array = Db::name('Step')
					->field('s.*,a.name as check_name')
					->alias('s')
					->join('Admin a', 'a.id = s.flow_uid', 'LEFT')
					->order('s.sort asc')
					->where(array('s.action_id' => $id, 's.type' => 2, 's.delete_time' => 0))
					->select()->toArray();
				foreach ($step_array as $kk => &$vv) {		
					$vv['start_time'] = date('Y-m-d', $vv['start_time']);
					$vv['end_time'] = date('Y-m-d', $vv['end_time']);
					$flow_names = Db::name('Admin')->where([['id','in',$vv['flow_ids']]])->column('name');
					$vv['flow_names'] = implode(',',$flow_names);	
				}	
					
				View::assign('step_array', $step_array);
				View::assign('detail', $detail);
				View::assign('id', $id);
				return view();
			}
		}
    }

    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = (new ProjectList())->detail($id);
        if (empty($detail)) {
            return to_assign(1, '项目不存在');
        } else {
            $tids = Db::name('ProjectTask')->where([['project_id', '=', $detail['id']], ['delete_time', '=', 0]])->column('id');
            $detail['schedules'] = Db::name('Schedule')->where([['tid', 'in', $tids], ['delete_time', '=', 0]])->count();
            $detail['hours'] = Db::name('Schedule')->where([['tid', 'in', $tids], ['delete_time', '=', 0]])->sum('labor_time');
            $detail['plan_hours'] = Db::name('ProjectTask')->where([['project_id', '=', $detail['id']], ['delete_time', '=', 0]])->sum('plan_hours');
			
			$detail['tasks'] = Db::name('ProjectTask')->where([['project_id', '=', $detail['id']],['delete_time', '=', 0]])->count();
            $detail['tasks_finish'] = Db::name('ProjectTask')->where([['project_id', '=', $detail['id']],['flow_status', '>', 2], ['delete_time', '=', 0]])->count();
            $detail['tasks_unfinish'] = $detail['tasks'] - $detail['tasks_finish'];
			
            $task_map = [];
            $task_map[] = ['project_id', '=', $detail['id']];
            $task_map[] = ['delete_time', '=', 0];
            //判断是否是创建者或者负责人
            $role = 0;
            if ($detail['director_uid'] == $this->uid) {
                $role = 1; //负责人
            }
            if ($detail['admin_id'] == $this->uid) {
                $role = 2; //创建人
            }
			$auth = isAuth($this->uid,'project_admin');
			if ($auth == 1) {
                $role = 3; //项目管理员
            }

			//相关附件
			$file_array = Db::name('ProjectFile')
                ->field('mf.id,mf.topic_id,mf.admin_id,f.name,f.filesize,f.filepath,f.fileext,f.create_time,f.admin_id,a.name as admin_name')
                ->alias('mf')
                ->join('File f', 'mf.file_id = f.id', 'LEFT')
                ->join('Admin a', 'mf.admin_id = a.id', 'LEFT')
                ->order('mf.create_time desc')
                ->where(array('mf.topic_id' => $id, 'mf.module' => 'project'))
                ->select()->toArray();
				
			//相关链接
			$link_array = Db::name('ProjectLink')
                ->field('i.id,i.topic_id,i.admin_id,i.desc,i.url,a.name as admin_name')
                ->alias('i')
                ->join('Admin a', 'i.admin_id = a.id', 'LEFT')
                ->order('i.create_time desc')
                ->where(array('i.topic_id' => $id, 'i.module' => 'project', 'delete_time' => 0))
                ->select()->toArray();
		
			//项目阶段			
			$step_array = Db::name('Step')
				->field('s.*,a.name as check_name')
				->alias('s')
				->join('Admin a', 'a.id = s.flow_uid', 'LEFT')
				->order('s.sort asc')
				->where(array('s.action_id' => $id, 's.type' => 2, 's.delete_time' => 0))
				->select()->toArray();
		
			//阶段操作记录			
			$step_record = Db::name('StepRecord')
				->field('s.*,a.name as check_name,p.flow_name')
				->alias('s')
				->join('Admin a', 'a.id = s.check_uid', 'LEFT')
				->join('Step p', 'p.id = s.step_id', 'LEFT')
				->order('s.check_time asc')
				->where(array('s.action_id' => $id, 's.type' => 2))
				->select()->toArray();		
			foreach ($step_record as $kk => &$vv) {		
				$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
				$vv['status_str'] = '提交';
				if($vv['status'] == 1){
					$vv['status_str'] = '确认完成';
				}
				else if($vv['status'] == 2){
					$vv['status_str'] = '回退';
				}
				if($vv['status'] == 3){
					$vv['status_str'] = '撤销';
				}
				if($vv['content'] == ''){
					$vv['content'] = '无';
				}
			}
			
			//当前项目阶段
			$step = Db::name('Step')->where(array('action_id' => $id, 'type' => 2, 'sort' => $detail['step_sort'],'delete_time'=>0))->find();
			if(!empty($step)){
				$step['check_name'] = Db::name('Admin')->where(['id' => $step['flow_uid']])->value('name');
				$flow_names = Db::name('Admin')->where([['id','in',$step['flow_ids']]])->column('name');
				$step['flow_names'] = implode(',',$flow_names);
				if ($this->uid == $step['flow_uid']){		
					$is_check_admin = 1;
				}
			}
				
            View::assign('file_array', $file_array);
            View::assign('link_array', $link_array);
            View::assign('step', $step);
            View::assign('step_array', $step_array);
			View::assign('step_record', $step_record);
            View::assign('detail', $detail);
            View::assign('role', $role);
            View::assign('id', $id);
            return view();
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $count_task = Db::name('ProjectTask')->where([['project_id', '=', $id], ['delete_time', '=', 0]])->count();
            if ($count_task > 0) {
                return to_assign(1, "该项目下有关联的任务，无法删除");
            }
            $detail = Db::name('Project')->where('id', $id)->find();
            if ($detail['admin_id'] != $this->uid) {
                return to_assign(1, "你不是该项目的创建人，无权限删除");
            }
            if (Db::name('Project')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                $log_data = array(
                    'module' => 'project',
                    'field' => 'delete',
                    'action' => 'delete',
                    'project_id' => $detail['id'],
                    'admin_id' => $this->uid,
                    'old_content' => '',
                    'new_content' => $detail['name'],
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
}
