<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);
namespace app\home\model;

use think\Model;
use think\facade\Db;
use dateset\Dateset;

class AdminLog extends Model
{
    public function get_log_list($param = [])
    {
        $rows = empty($param['limit']) ? get_config('app.pages') : $param['limit'];
        $list = Db::name('AdminLog')
            ->field("a.id,a.uid,a.type,a.subject,a.action,a.create_time,u.name")
			->alias('a')
			->join('Admin u', 'a.uid = u.id')
            ->order('a.create_time desc')
            ->paginate($rows, false, ['query' => $param])
			->each(function($item, $key){
				$item['content'] = $item['name']. $item['action'] . '了' . $item['subject'];
				$item['times'] = (new Dateset())->time_trans($item['create_time']);
				return $item;
			});
        return $list;
    }
}
