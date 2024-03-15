<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);
namespace app\Project\model;
use think\facade\Db;
use think\Model;

class ProjectDocument extends Model
{
    //列表
    function list($param) {
        $where = array();
        $whereOr  = array();
        $map1 = [];
        $map2 = [];
        if (!empty($param['project_id'])) {
            $where[] = ['project_id', '=', $param['project_id']];
        } else {
            $project_ids = Db::name('ProjectUser')->where(['uid' => $param['uid'], 'delete_time' => 0])->column('project_id');
            $map1[] = ['admin_id', '=', $param['uid']];
            $map2[] = ['project_id', 'in', $project_ids];
			$whereOr =[$map1,$map2];
        }
        if (!empty($param['keywords'])) {
            $where[] = ['title|content', 'like', '%' . $param['keywords'] . '%'];
        }
        $where[] = ['delete_time', '=', 0];
		
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('ProjectDocument')
			->where(function ($query) use ($whereOr) {
				if (!empty($whereOr))
					$query->whereOr($whereOr);
				})
			->where($where)
            ->withoutField('content,md_content')
            ->order('id desc')
            ->paginate($rows, false, ['query' => $param])
            ->each(function ($item, $key) {
                $item['project_name'] = Db::name('Project')->where(['id' => $item['project_id']])->value('name');
                $item['admin_name'] = Db::name('Admin')->where(['id' => $item['admin_id']])->value('name');
                $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
                return $item;
            });
        return $list;
    }
	
    //详情
    public function detail($id)
    {
        $detail = Db::name('ProjectDocument')->where(['id' => $id])->find();
        if (!empty($detail)) {
			$detail['project_name'] = '-';
            if ($detail['project_id'] > 0) {
                $detail['project_name'] = Db::name('Project')->where(['id' => $detail['project_id']])->value('name');
            }
            $detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
            $detail['times'] = time_trans($detail['create_time']);
            $detail['create_time'] = date('Y-m-d', $detail['create_time']);
        }
        return $detail;
    }
}
