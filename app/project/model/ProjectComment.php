<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);
namespace app\Project\model;

use think\Model;

class ProjectComment extends Model
{
    public function get_list($param = [])
    {
        $where = array();
        $where['a.module'] = $param['m'];
		$where['a.topic_id'] = $param['tid'];
        $where['a.delete_time'] = 0;
        $content = \think\facade\Db::name('ProjectComment')
			->field('a.*,u.name,u.thumb,pu.name as pname')
            ->alias('a')
            ->join('Admin u', 'u.id = a.admin_id')
            ->leftjoin('Admin pu', 'pu.id = a.padmin_id')
            ->order('a.create_time desc')
            ->where($where)
            ->select()->toArray();
        foreach ($content as $k => &$v) {
            $v['times'] = time_trans($v['create_time']);
			$v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
			if($v['update_time']>0){
				$v['update_time'] = '，最后编辑时间:'.time_trans($v['update_time']);
			}
			else{
				$v['update_time'] = '';
			}
        }
        return $content;
    }
}
