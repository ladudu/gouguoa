<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);
namespace app\project\model;

use think\facade\Db;
use think\Model;

class ProjectLog extends Model
{
    public static $Sourse = [
        'project' => [
            'status' => ['','未开始', '进行中', '已完成', '已关闭'],
            'field_array' => [
                'director_uid' => array('icon' => 'icon-xueshengzhuce', 'title' => '负责人'),
                'start_time' => array('icon' => 'icon-kaoshijihua', 'title' => '预计开始时间'),
                'end_time' => array('icon' => 'icon-kaoshijihua', 'title' => '预计结束时间'),
                'name' => array('icon' => 'icon-wodedianping', 'title' => '标题'),
                'status' => array('icon' => 'icon-wodedianping', 'title' => '状态'),
                'content' => array('icon' => 'icon-wodedianping', 'title' => '描述'),
                'file' => array('icon' => 'icon-sucaiziyuan', 'title' => '文件'),
				'file_ids' => array('icon' => 'icon-sucaiziyuan', 'title' => '文件'),
                'contract_id' => array('icon' => 'icon-hetongguanli', 'title' => '合同'),
                'customer_id' => array('icon' => 'icon-jiaoshiguanli', 'title' => '客户'),
                'link' => array('icon' => 'icon-sucaiziyuan', 'title' => '链接'),
                'user' => array('icon' => 'icon-xueshengzhuce', 'title' => '成员'),
                'new' => array('icon' => 'icon-zidingyishezhi', 'title' => '项目'),
                'delete' => array('icon' => 'icon-shanchu', 'title' => '项目'),
            ]],
        'task' => [
            'priority' => ['', '低', '中', '高', '紧急'],
            'flow_status' => ['', '未开始', '进行中', '已完成', '已拒绝', '已关闭'],
            'type' => ['', '需求', '设计', '研发', '缺陷'],
            'field_array' => [
                'director_uid' => array('icon' => 'icon-xueshengzhuce', 'title' => '负责人'),
                'assist_admin_ids' => array('icon' => 'icon-xueshengbaoming', 'title' => '协作人'),
                'end_time' => array('icon' => 'icon-kaoshijihua', 'title' => '预计结束时间'),
                'title' => array('icon' => 'icon-wodedianping', 'title' => '标题'),
                'flow_status' => array('icon' => 'icon-wodedianping', 'title' => '任务状态'),
                'plan_hours' => array('icon' => 'icon-wodedianping', 'title' => '工时'),
                'priority' => array('icon' => 'icon-wodedianping', 'title' => '等级'),
                'type' => array('icon' => 'icon-wodedianping', 'title' => '任务类型'),
                'cate' => array('icon' => 'icon-wodedianping', 'title' => '任务类别'),
                'done_ratio' => array('icon' => 'icon-wodedianping', 'title' => '完成进度'),
                'project_id' => array('icon' => 'icon-wodedianping', 'title' => '关联项目'),
                'before_task' => array('icon' => 'icon-wodedianping', 'title' => '前置任务'),
                'content' => array('icon' => 'icon-wodedianping', 'title' => '描述'),
                'file' => array('icon' => 'icon-sucaiziyuan', 'title' => '文件'),
				'file_ids' => array('icon' => 'icon-sucaiziyuan', 'title' => '文件'),
                'new' => array('icon' => 'icon-zidingyishezhi', 'title' => '任务'),
                'delete' => array('icon' => 'icon-shanchu', 'title' => '任务'),
            ]],
		'document' => [
            'field_array' => [
                'title' => array('icon' => 'icon-wodedianping', 'title' => '标题'),
                'project_id' => array('icon' => 'icon-wodedianping', 'title' => '关联项目'),
                'content' => array('icon' => 'icon-wodedianping', 'title' => '描述'),
				'file_ids' => array('icon' => 'icon-sucaiziyuan', 'title' => '文件'),
                'new' => array('icon' => 'icon-zidingyishezhi', 'title' => '任务'),
                'delete' => array('icon' => 'icon-shanchu', 'title' => '任务'),
            ]]
    ];

    public function get_list($param = [])
    {
        $where = array();
        $where['a.module'] = $param['m'];
        $where['a.' . $param['m'] . '_id'] = $param['tid'];
        $content = Db::name('ProjectLog')
            ->field('a.*,u.name')
            ->alias('a')
            ->join('Admin u', 'u.id = a.admin_id')
            ->order('a.create_time desc')
            ->where($where)
            ->select()->toArray();
        $sourse = self::$Sourse[$param['m']];
        $action = get_config('log.type_action');
        $field_array = $sourse['field_array'];
        $data = [];
        foreach ($content as $k => $v) {
            if (isset($sourse[$v['field']])) {
                $v['old_content'] = $sourse[$v['field']][$v['old_content']];
                $v['new_content'] = $sourse[$v['field']][$v['new_content']];
            }
            if (strpos($v['field'], '_time') !== false) {
                if ($v['old_content'] == '') {
                    $v['old_content'] = '未设置';
                }
                $v['new_content'] = date('Y-m-d', (int) $v['new_content']);
            }
            if (strpos($v['field'], '_uid') !== false) {
                $v['old_content'] = Db::name('Admin')->where(['id' => $v['old_content']])->value('name');
                $v['new_content'] = Db::name('Admin')->where(['id' => $v['new_content']])->value('name');
            }
            if ($v['field'] == 'user') {
                $v['new_content'] = Db::name('Admin')->where(['id' => $v['new_content']])->value('name');
            }
            if ($v['field'] == 'project_id') {
                $v['old_content'] = Db::name('Project')->where(['id' => $v['old_content']])->value('name');
                $v['new_content'] = Db::name('Project')->where(['id' => $v['new_content']])->value('name');
            }
			if ($v['field'] == 'before_task') {
                $v['old_content'] = Db::name('ProjectTask')->where(['id' => $v['old_content']])->value('title');
                $v['new_content'] = Db::name('ProjectTask')->where(['id' => $v['new_content']])->value('title');
            }
            if ($v['field'] == 'cate') {
                $v['old_content'] = Db::name('WorkCate')->where(['id' => $v['old_content']])->value('title');
                $v['new_content'] = Db::name('WorkCate')->where(['id' => $v['new_content']])->value('title');
            }
            if ($v['field'] == 'done_ratio') {
                $v['old_content'] = $v['old_content'] . '%';
                $v['new_content'] = $v['new_content'] . '%';
            }
            if (strpos($v['field'], '_ids') !== false) {
                $old_ids = Db::name('Admin')->where('id', 'in', $v['old_content'])->column('name');
                $v['old_content'] = implode(',', $old_ids);
                $new_ids = Db::name('Admin')->where('id', 'in', $v['new_content'])->column('name');
                $v['new_content'] = implode(',', $new_ids);
            }
            if ($v['old_content'] == '' || $v['old_content'] == null) {
                $v['old_content'] = '未设置';
            }
            if ($v['new_content'] == '' || $v['new_content'] == null) {
                $v['new_content'] = '未设置';
            }
            $v['action'] = $action[$v['action']];
            $v['icon'] = $field_array[$v['field']]['icon'];
            $v['title'] = $field_array[$v['field']]['title'];
            $v['times'] = time_trans($v['create_time']);
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $data[] = $v;
        }
        return $data;
    }

    public function project_log($param = [])
    {
        $task_ids = Db::name('ProjectTask')->where(['project_id' => $param['topic_id'], 'delete_time' => 0])->column('id');
        $document_ids = Db::name('ProjectDocument')->where(['project_id' => $param['topic_id'], 'delete_time' => 0])->column('id');

        $where1 = [];
        $where2 = [];
        $where3 = [];

        $where1[] = ['a.module', '=', 'project'];
        $where1[] = ['a.project_id', '=', $param['topic_id']];

        $where2[] = ['a.module', '=', 'task'];
        $where2[] = ['a.task_id', 'in', $task_ids];
		
		$where3[] = ['a.module', '=', 'document'];
        $where3[] = ['a.document_id', 'in', $document_ids];
        $page = intval($param['page']);
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $content = Db::name('ProjectLog')
            ->field('a.*,u.name,u.thumb')
            ->alias('a')
            ->join('Admin u', 'u.id = a.admin_id')
            ->order('a.create_time desc')
            ->whereOr([$where1, $where2, $where3])
            ->page($page, $rows)
            ->select()->toArray();

        $module = [
            'project' => '',
            'task' => '任务',
            'document' => '文档',
        ];
        $action = get_config('log.type_action');
        $data = [];
        foreach ($content as $k => $v) {
            $sourse = self::$Sourse[$v['module']];
            $field_array = $sourse['field_array'];
            if (isset($sourse[$v['field']])) {
                $v['old_content'] = $sourse[$v['field']][$v['old_content']];
                $v['new_content'] = $sourse[$v['field']][$v['new_content']];
            }
            if (strpos($v['field'], '_time') !== false) {
                if ($v['old_content'] == '') {
                    $v['old_content'] = '未设置';
                }
				else{
					$v['old_content'] = date('Y-m-d', (int) $v['old_content']);
				}
                $v['new_content'] = date('Y-m-d', (int) $v['new_content']);
            }
            if (strpos($v['field'], '_uid') !== false) {
                $v['old_content'] = Db::name('Admin')->where(['id' => $v['old_content']])->value('name');
                $v['new_content'] = Db::name('Admin')->where(['id' => $v['new_content']])->value('name');
            }
            if ($v['field'] == 'user') {
                $v['new_content'] = Db::name('Admin')->where(['id' => $v['new_content']])->value('name');
            }
            if ($v['field'] == 'project_id') {
                $v['old_content'] = Db::name('Project')->where(['id' => $v['old_content']])->value('name');
                $v['new_content'] = Db::name('Project')->where(['id' => $v['new_content']])->value('name');
            }
            if ($v['field'] == 'cate') {
                $v['old_content'] = Db::name('WorkCate')->where(['id' => $v['old_content']])->value('title');
                $v['new_content'] = Db::name('WorkCate')->where(['id' => $v['new_content']])->value('title');
            }
			if ($v['field'] == 'contract_id') {
                $v['old_content'] = Db::name('Contract')->where(['id' => $v['old_content']])->value('name');
                $v['new_content'] = Db::name('Contract')->where(['id' => $v['new_content']])->value('name');
            }
			if ($v['field'] == 'customer_id') {
                $v['old_content'] = Db::name('Customer')->where(['id' => $v['old_content']])->value('name');
                $v['new_content'] = Db::name('Customer')->where(['id' => $v['new_content']])->value('name');
            }
            if ($v['field'] == 'done_ratio') {
                $v['old_content'] = $v['old_content'] . '%';
                $v['new_content'] = $v['new_content'] . '%';
            }
            if (strpos($v['field'], '_ids') !== false) {
                $old_ids = Db::name('Admin')->where('id', 'in', $v['old_content'])->column('name');
                $v['old_content'] = implode(',', $old_ids);
                $new_ids = Db::name('Admin')->where('id', 'in', $v['new_content'])->column('name');
                $v['new_content'] = implode(',', $new_ids);
            }
            if ($v['old_content'] == '' || $v['old_content'] == null) {
                $v['old_content'] = '未设置';
            }
            if ($v['new_content'] == '' || $v['new_content'] == null) {
                $v['new_content'] = '未设置';
            }
            $v['module_name'] = $module[$v['module']];

            $v['topic'] = '';
            $v['topic_title'] = '';
            $v['url'] = '';
            if ($v['module'] == 'task') {
                $v['topic'] = 'T' . $v['task_id'];
                $v['topic_title'] = Db::name('ProjectTask')->where('id', $v['task_id'])->value('title');
                $v['url'] = '/project/task/view/id/' . $v['task_id'];
            }

            $v['action'] = $action[$v['action']];
            $v['icon'] = $field_array[$v['field']]['icon'];
            $v['title'] = $field_array[$v['field']]['title'];
            $v['times'] = time_trans($v['create_time']);
            $v['create_time'] = date('Y-m-d', $v['create_time']);
            $data[] = $v;
        }
        return $data;
    }
}
