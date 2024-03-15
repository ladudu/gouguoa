<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\home\controller;

use app\api\BaseController;
use think\facade\Db;

class api extends BaseController
{
    //首页公告
    public function get_note_list()
    {
        $list = Db::name('Note')
            ->field('a.id,a.title,a.create_time,c.title as cate_title')
            ->alias('a')
            ->join('note_cate c', 'a.cate_id = c.id')
            ->where(['a.status' => 1])
            ->order('a.end_time desc,a.sort desc,a.create_time desc')
            ->limit(8)
            ->select()->toArray();
        foreach ($list as $key => $val) {
            $list[$key]['create_time'] = date('Y-m-d H:i', $val['create_time']);
        }
        $res['data'] = $list;
        return table_assign(0, '', $res);
    }
	
	//首页知识列表
    public function get_article_list()
    {
		$prefix = get_config('database.connections.mysql.prefix');//判断是否安装了文章模块
		$exist = Db::query('show tables like "'.$prefix.'article"');
		$res['data'] = [];
		if($exist){
			$list = Db::name('Article')
				->field('a.id,a.title,a.create_time,a.read,c.title as cate_title')
				->alias('a')
				->join('article_cate c', 'a.cate_id = c.id')
				->where(['a.delete_time' => 0])
				->order('a.id desc')
				->limit(8)
				->select()->toArray();
			foreach ($list as $key => $val) {
				$list[$key]['create_time'] = date('Y-m-d H:i', $val['create_time']);
			}
			$res['data'] = $list;			
		}
		return table_assign(0, '', $res);
	}

	function isAuthProject($uid)
	{
		if($uid == 1){
			return 1;
		}
		$map = [];
		$map[] = ['name', '=', 'project_admin'];
		$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")];
		$count = Db::name('DataAuth')->where($map)->count();
		return $count;
	}
    //首页项目
    public function get_project_list()
    {
		$prefix = get_config('database.connections.mysql.prefix');//判断是否安装了项目模块
		$exist = Db::query('show tables like "'.$prefix.'project"');
		$res['data'] = [];
		if($exist){
			$project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
			$where =[];
			$where[] = ['a.delete_time', '=', 0];
			if($this->isAuthProject($this->uid)==0){
				$where[] = ['a.id', 'in', $project_ids];
			}
			$list = Db::name('Project')
				->field('a.id,a.name,a.status,a.create_time,a.start_time,a.end_time,u.name as director_name')
				->alias('a')
				->join('Admin u', 'a.director_uid = u.id')
				->where($where)
				->order('a.id desc')
				->limit(8)
				->select()->toArray();
			foreach ($list as $key => &$val) {
				$val['create_time'] = date('Y-m-d H:i', $val['create_time']);
				if($val['end_time']>0){
					$val['plan_time'] = date('Y-m-d', $val['start_time']) . ' 至 ' . date('Y-m-d', $val['end_time']);
				}
				else{
					$val['plan_time'] = '-';
				}
				$val['status_name'] = \app\project\model\Project::$Status[(int) $val['status']];
			}
			$res['data'] = $list;
		}
        return table_assign(0, '', $res);
    }
	
    //首页任务
    public function get_task_list()
    {
		$prefix = get_config('database.connections.mysql.prefix');//判断是否安装了项目模块
		$exist = Db::query('show tables like "'.$prefix.'project_task"');
		$res['data'] = [];
		if($exist){
			$where = array();
			$whereOr = array();
			$map1 = [];
			$map2 = [];
			$map3 = [];
			$map1[] = ['admin_id', '=', $this->uid];
			$map2[] = ['director_uid', '=', $this->uid];
			$map3[] = ['', 'exp', Db::raw("FIND_IN_SET({$this->uid},assist_admin_ids)")];
			if($this->isAuthProject($this->uid)==0){
				$whereOr =[$map1,$map2,$map3];
			}
			$where[] = ['delete_time', '=', 0];
			$list = Db::name('ProjectTask')
				->where(function ($query) use ($whereOr) {
					if (!empty($whereOr))
						$query->whereOr($whereOr);
					})
				->where($where)
				->withoutField('content,md_content')
				->order('flow_status asc')
				->order('id desc')
				->limit(8)
				->select()->toArray();
				foreach ($list as $key => &$val) {
					$val['director_name'] = Db::name('Admin')->where(['id' => $val['director_uid']])->value('name');
					if($val['end_time']>0){
						$val['end_time'] = date('Y-m-d', $val['end_time']);
					}
					else{
						$val['end_time'] = '-';
					}
					$val['flow_name'] = \app\project\model\ProjectTask::$FlowStatus[(int) $val['flow_status']];
				}
			$res['data'] = $list;
		}
        return table_assign(0, '', $res);
    }
	
    //获取访问记录
    public function get_view_data()
    {
        $param = get_params();
        $first_time = time();
        $second_time = $first_time - 86400;
        $three_time = $first_time - 86400 * 365;
        $begin_first = strtotime(date('Y-m-d', $first_time) . " 00:00:00");
        $end_first = strtotime(date('Y-m-d', $first_time) . " 23:59:59");
        $begin_second = strtotime(date('Y-m-d', $second_time) . " 00:00:00");
        $end_second = strtotime(date('Y-m-d', $second_time) . " 23:59:59");
        $begin_three = strtotime(date('Y-m-d', $three_time) . " 00:00:00");
        $data_first = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_first,$end_first")->select();
        $data_second = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_second,$end_second")->select();
        $data_three = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_three,$end_first")->select();
        return to_assign(0, '', ['data_first' => hour_document($data_first), 'data_second' => hour_document($data_second), 'data_three' => date_document($data_three)]);
    }
	
	//获取员工活跃数据
    public function get_view_log()
    {		
        $times = strtotime("-30 day");
        $where = [];
        $where[] = ['uid','<>',1];
        $where[] = ['create_time', '>', $times];
        $list = Db::name('AdminLog')->field("id,uid")->where($where)->select();
        $logs = array();
        foreach ($list as $key => $value) {
            $uid = $value['uid'];
            if (empty($logs[$uid])) {
                $logs[$uid]['count'] = 1;
                $logs[$uid]['name'] = Db::name('Admin')->where('id',$uid)->value('name');
            } else {
                $logs[$uid]['count'] += 1;
            }
        }
        $counts = array_column($logs, 'count');
        array_multisort($counts, SORT_DESC, $logs);
        //攫取前10
        $data_logs = array_slice($logs, 0, 10);
        return to_assign(0, '', ['data_logs' => $data_logs]);
    }

}
