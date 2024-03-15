<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use think\facade\Db;
use think\facade\View;

class Log extends BaseController
{
    //管理员操作日志
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['u.name|a.param_id|a.uid', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['action'])) {
                $where[] = ['a.action','=',$param['action']];
            }
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $list = DB::name('AdminLog')
                ->field("a.*,u.name")
				->alias('a')
				->join('Admin u', 'a.uid = u.id')
                ->order('a.create_time desc')
                ->where($where)
                ->paginate($rows, false, ['query' => $param])
				->each(function($item, $key){
					$item['content'] = $item['name']. $item['action'] . '了' . $item['subject'];
					$item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
					$param_array = json_decode($item['param'], true);
					if(is_array($param_array)){
						$param_value = [];
						foreach ($param_array as $key => $value) {
							if (is_array($value)) {
								$value = implode(',', $value);
							}
							$param_value[] = $key . ':' . $value;
						}
						$item['param'] = implode(' & ',$param_value);
					}
					else{
						$item['param'] = $param_array;
					}
					return $item;
				});
            return table_assign(0, '', $list);
        } else {
			$type_action = get_config('log.type_action');
			View::assign('type_action', $type_action);
            return view();
        }
    }
}
