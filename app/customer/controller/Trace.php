<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\customer\controller;

use app\base\BaseController;
use app\customer\model\CustomerTrace;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Trace extends BaseController
{	
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.title|c.name', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['stage'])) {
                $where[] = ['a.stage', '=', $param['stage']];
            }
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['a.expected_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
			}
            $where[] = ['a.delete_time', '=', 0];
			
			$uid = $this->uid;
			$auth = isAuth($uid,'customer_admin');
			
			if (empty($param['uid'])) {
				if($auth==0){
					$dids = get_department_role($this->uid);
					if(!empty($dids)){
						$whereOr[] =['c.belong_did', 'in', $dids];
					}
					$whereOr[] =['c.belong_uid', '=', $uid];				
					$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',c.share_ids)")];
				}
			}
			else{
				$where[] = ['a.belong_uid', '=', $param['uid']];
			}
			
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$content = CustomerTrace::where($where)
				->where(function ($query) use($whereOr) {
					$query->whereOr($whereOr);
				})
                ->field('a.*,c.name as customer')
                ->alias('a')
                ->join('customer c', 'a.cid = c.id')
                ->order('a.create_time desc')
				->paginate($rows, false, ['query' => $param])
				->each(function ($item, $key) {
					$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
					$item->create_time = date('Y-m-d H:i:s', (int) $item->create_time);
					$item->follow_time = date('Y-m-d H:i', (int) $item->follow_time);
					$item->next_time = date('Y-m-d H:i', (int) $item->next_time);
					$item->stage_name = CustomerTrace::$Stage[(int) $item->stage];
					$item->type_name = CustomerTrace::$Type[(int) $item->type];
					$item->chance='-';
					if($item->chance_id>0){
						$item->chance=Db::name('CustomerChance')->where(['id' => $item->chance_id])->value('title');
					}
				});
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }
}
