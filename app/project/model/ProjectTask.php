<?php
namespace app\project\model;

use think\facade\Db;
use think\Model;

class ProjectTask extends Model
{
    const ZERO = 0; //#648A8D
    const ONE = 1; //#4AC8BE
    const TWO = 2; //#409CDE
    const THREE = 3; //#C0DB38
    const FOUR = 4; //#4DCE58
    const FIVE = 5; //#FEC939
    const SIX = 6; //#8838DA
    const SEVEN = 7; //#FD6206
    const EIGHT = 8; //#F03347
    const NINE = 9; //#A38B82

    public static $Priority = [
        self::ZERO => '未设置',
        self::ONE => '低',
        self::TWO => '中',
        self::THREE => '高',
        self::FOUR => '紧急',
    ];
    public static $FlowStatus = [
        self::ZERO => '未设置',
        self::ONE => '未开始',
        self::TWO => '进行中',
        self::THREE => '已完成',
        self::FOUR => '已拒绝',
        self::FIVE => '已关闭',
    ];
	
	public static $Type = [
        self::ZERO => '未设置',
        self::ONE => '需求',
        self::TWO => '设计',
        self::THREE => '研发',
        self::FOUR => '缺陷',
    ];

    //列表
    function list($param) {
        $where = array();
        $whereOr = array();
        $map1 = [];
        $map2 = [];
        $map3 = [];
        $map4 = [];
        if (!empty($param['project_id'])) {
            $where[] = ['project_id', '=', $param['project_id']];
        } else {
			if (isAuthProject($param['uid'])==0) {
				$project_ids = Db::name('ProjectUser')->where(['uid' => $param['uid'], 'delete_time' => 0])->column('project_id');
				$map1[] = ['admin_id', '=', $param['uid']];
				$map2[] = ['director_uid', '=', $param['uid']];
				$map3[] = ['', 'exp', Db::raw("FIND_IN_SET({$param['uid']},assist_admin_ids)")];
				$map4[] = ['project_id', 'in', $project_ids];
				$whereOr =[$map1,$map2,$map3,$map4];
			}
        }
        if (!empty($param['flow_status'])) {
            $where[] = ['flow_status', '=', $param['flow_status']];
        }
        if (!empty($param['priority'])) {
            $where[] = ['priority', '=', $param['priority']];
        }
        if (!empty($param['cate'])) {
            $where[] = ['cate', '=', $param['cate']];
        }
        if (!empty($param['director_uid'])) {
            $where[] = ['director_uid', 'in', $param['director_uid']];
        }
        if (!empty($param['keywords'])) {
            $where[] = ['title|content', 'like', '%' . $param['keywords'] . '%'];
        }		
		if (!empty($param['task_id'])) {
            $where[] = ['id', '<>', $param['task_id']];
        }
		if (!empty($param['set_pid'])) {
            $where[] = ['pid', '=', 0];
            $where[] = ['id', '<>', $param['set_pid']];
            $where[] = ['before_task', '<>', $param['set_pid']];
        }
		if (!empty($param['before_task'])) {
            $where[] = ['before_task', '=', $param['before_task']];
        }
        $where[] = ['delete_time', '=', 0];
		
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('ProjectTask')
			->where(function ($query) use ($whereOr) {
				if (!empty($whereOr))
					$query->whereOr($whereOr);
				})
			->where($where)
            ->withoutField('content,md_content')
            ->order('flow_status asc')
            ->order('id desc')
            ->paginate($rows, false, ['query' => $param])
            ->each(function ($item, $key) {
				$item['director_name'] = '-';
				if ($item['director_uid'] > 0) {
					$item['director_name'] = Db::name('Admin')->where(['id' => $item['director_uid']])->value('name');
				}
                $assist_admin_names = Db::name('Admin')->where([['id', 'in', $item['assist_admin_ids']]])->column('name');
                if (empty($assist_admin_names)) {
                    $item['assist_admin_names'] = '-';
                } else {
                    $item['assist_admin_names'] = implode(',', $assist_admin_names);
                }
				if ($item['project_id'] == 0) {
                    $item['project_name'] = '-';
                } else {
                    $item['project_name'] = Db::name('Project')->where(['id' => $item['project_id'],'delete_time' => 0])->value('name');
                }
                $item['cate_name'] = Db::name('WorkCate')->where(['id' => $item['cate']])->value('title');
				$item['after_num'] = Db::name('ProjectTask')->where(['before_task'=>$item['id'],'delete_time' => 0])->count();
				if($item['after_num']==1){
					$item['after_id'] = Db::name('ProjectTask')->where(['before_task'=>$item['id'],'delete_time' => 0])->value('id');
				}
				$item['delay'] = 0;
				if ($item['end_time'] > 0) {
					$item['end_time'] = date('Y-m-d', $item['end_time']);
					if ($item['over_time'] > 0 && $item['flow_status'] < 4) {
						$item['delay'] = countDays($item['end_time'], date('Y-m-d', $item['over_time']));
					}
					if ($item['over_time'] == 0 && $item['flow_status'] < 4) {
						$item['delay'] = countDays($item['end_time']);
					}
				}
				else{
					$item['end_time'] = '-';
				}
                $item['priority_name'] = self::$Priority[(int) $item['priority']];
                $item['flow_name'] = self::$FlowStatus[(int) $item['flow_status']];
                $item['type_name'] = self::$Type[(int) $item['type']];
                return $item;
            });
        return $list;
    }
    //详情
    public function detail($id)
    {
        $detail = Db::name('ProjectTask')->where(['id' => $id])->find();
        if (!empty($detail)) {
            $detail['before_task_name'] = '';
            $detail['project_name'] = '';
            if ($detail['project_id'] > 0) {
                $detail['project_name'] = Db::name('Project')->where(['id' => $detail['project_id']])->value('name');
            }
			
			if ($detail['before_task'] > 0) {
                $before_task = Db::name('ProjectTask')->where(['id' => $detail['before_task']])->find();
                $detail['before_task_name'] = $before_task['title'];
                $detail['before_task_flow_status'] = $before_task['flow_status'];
                $detail['before_task_flow_name'] = self::$FlowStatus[(int) $before_task['flow_status']];
            }
			
            $detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
            $detail['work_hours'] = Db::name('Schedule')->where(['delete_time' => 0, 'tid' => $detail['id']])->sum('labor_time');
            $detail['cate_name'] = Db::name('WorkCate')->where(['id' => $detail['cate']])->value('title');
			
			$detail['director_name']= '';
			if($detail['director_uid'] > 0){
				$detail['director_name'] = Db::name('Admin')->where(['id' => $detail['director_uid']])->value('name');
			}
            $detail['logs'] = Db::name('ProjectLog')->where(['module' => 'task', 'task_id' => $detail['id']])->count();
            $detail['comments'] = Db::name('ProjectComment')->where(['module' => 'task', 'delete_time' => 0, 'topic_id' => $detail['id']])->count();
            $detail['assist_admin_names'] = '';
            if (!empty($detail['assist_admin_ids'])) {
                $assist_admin_names = Db::name('Admin')->where('id', 'in', $detail['assist_admin_ids'])->column('name');
                $detail['assist_admin_names'] = implode(',', $assist_admin_names);
            }
            $detail['priority_name'] = self::$Priority[(int) $detail['priority']];
            $detail['flow_name'] = self::$FlowStatus[(int) $detail['flow_status']];
			$detail['type_name'] = self::$Type[(int) $detail['type']];
            $detail['times'] = time_trans($detail['create_time']);
			$detail['delay'] = 0;
			if($detail['end_time']>0){
				$detail['end_time'] = date('Y-m-d', $detail['end_time']);
				if ($detail['over_time'] > 0 && $detail['flow_status'] < 4) {
					$detail['delay'] = countDays($detail['end_time'], date('Y-m-d', $detail['over_time']));
				}
				if ($detail['over_time'] == 0 && $detail['flow_status'] < 4) {
					$detail['delay'] = countDays($detail['end_time']);
				}
			}
			else{
				$detail['end_time'] = '';
			}
        }
        return $detail;
    }
}
