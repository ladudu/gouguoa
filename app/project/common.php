<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
/**
======================
 *模块数据获取公共文件
======================
 */
use think\facade\Db;
//是否是项目管理员,count>1即有权限
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
//写入日志
function add_project_log($uid,$module,$param,$old)
{
	$log_data = [];
	$key_array = ['id', 'create_time', 'update_time', 'delete_time', 'over_time', 'md_content'];
	foreach ($param as $key => $value) {
		if (!in_array($key, $key_array)) {
			$log_data[] = array(
				'module' => $module,
				'field' => $key,
				$module . '_id' => $param['id'],
				'admin_id' => $uid,
				'old_content' => $old[$key],
				'new_content' => $value,
				'create_time' => time(),
			);
		}
	}
	Db::name('ProjectLog')->strict(false)->field(true)->insertAll($log_data);
}
//读取项目
function get_project($uid = 0)
{
    $map = [];
    $map[] = ['delete_time', '=', 0];
    if ($uid > 0) {
        $project_ids = Db::name('ProjectUser')->where(['uid' => $uid, 'delete_time' => 0])->column('project_id');
        $map[] = ['id', 'in', $project_ids];
    }
    $project = Db::name('Project')->where($map)->select()->toArray();
    return $project;
}
//读取工作分类列表
function work_cate()
{
    $cate = Db::name('WorkCate')->where(['status' => 1])->order('id desc')->select()->toArray();
    return $cate;
}


//任务分配情况统计
function plan_count($arrData)
{
    $documents = array();
    foreach ($arrData as $index => $value) {
        $planTime = date("Y-m-d", $value['end_time']);
        if (empty($documents[$planTime])) {
            $documents[$planTime] = 1;
        } else {
            $documents[$planTime] += 1;
        }
    }
    return $documents;
}

//工时登记情况统计
function hour_count($arrData)
{
    $documents = array();
    foreach ($arrData as $index => $value) {
        $hourTime = date("Y-m-d", $value['start_time']);
        if (empty($documents[$hourTime])) {
            $documents[$hourTime] = $value['labor_time'] + 0;
        } else {
            $documents[$hourTime] += $value['labor_time'];
        }
        $documents[$hourTime] = round($documents[$hourTime], 2);
    }
    return $documents;
}

//燃尽图统计
function cross_count($arrData)
{
    $documents = array();
    foreach ($arrData as $index => $value) {
        $planTime = date("Y-m-d", $value['end_time']);
        if (empty($documents[$planTime])) {
            $documents[$planTime] = 1;
        } else {
            $documents[$planTime] += 1;
        }
    }
    return $documents;
}

//读取后置任务的ids
function admin_after_task_son($task_id = 0, $list = [])
{
    $task_ids = Db::name('ProjectTask')->where([['before_task','in',$task_id]])->column('id');
	if(!empty($task_ids)){
		$new_list = array_merge($list, $task_ids);
		$list = admin_after_task_son($task_ids, $new_list);
	}
	return $list;
}

//读取父任务的ids
function admin_parent_task($task_id = 0, $list = [])
{
    $pids = Db::name('ProjectTask')->where([['pid','in',$task_id]])->column('id');
	if(!empty($pids)){
		$new_list = array_merge($list, $pids);
		$list = admin_parent_task($pids, $new_list);
	}
	return $list;
}

//获取后置任务
function after_task($task_id)
{
    $list = Db::name('ProjectTask')->where('before_task',$task_id)->order('id desc')->select()->toArray();
    return $list;
}