<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\customer\controller;

use app\base\BaseController;
use app\customer\model\CustomerChance;
use app\customer\validate\CustomerChanceCheck;
use app\customer\model\CustomerTrace;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Chance extends BaseController
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
			$content = CustomerChance::where($where)
				->where(function ($query) use($whereOr) {
					$query->whereOr($whereOr);
				})
                ->field('a.*,c.name as customer')
                ->alias('a')
                ->join('customer c', 'a.cid = c.id')
                ->order('a.create_time desc')
				->paginate($rows, false, ['query' => $param])
				->each(function ($item, $key) {
					$item->belong_name = Db::name('Admin')->where(['id' => $item->belong_uid])->value('name');
					$item->create_time = date('Y-m-d H:i:s', (int) $item->create_time);
					$item->discovery_time = date('Y-m-d', (int) $item->discovery_time);
					$item->expected_time = date('Y-m-d', (int) $item->expected_time);
					$item->stage_name = CustomerTrace::$Stage[(int) $item->stage];
					$item->services_name = Db::name('Services')->where(['id' => $item->services_id])->value('title');
				});
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }

   	//添加销售机会
	public function chance_add()
    {
		$param = get_params();
        if (request()->isAjax()) {
			if(isset($param['discovery_time'])){
				$param['discovery_time'] = strtotime($param['discovery_time']);
			}
			if(isset($param['expected_time'])){
				$param['expected_time'] = strtotime($param['expected_time']);
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
				$old = CustomerChance::where(['id' => $param['id']])->find();
				if($this->uid!=$old['admin_id'] && get_user_role($this->uid,$old['admin_id'])==0){
					return to_assign(1, "只有所属员工或者部门负责人才有权限操作");
				}
				$res = CustomerChance::strict(false)->field(true)->update($param);
				if ($res) {
					add_log('edit', $param['id'], $param);
					to_log($this->uid,3,$param,$old);
					return to_assign();
				} else {
					return to_assign(1, '操作失败');
				}
            } else {
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
				$tid = CustomerChance::strict(false)->field(true)->insertGetId($param);
				if ($tid) {
					add_log('add', $tid, $param,'客户销售机会');
					$log_data = array(
						'field' => 'new',
						'action' => 'add',
						'type' => 3,
						'customer_id' => $param['cid'],
						'admin_id' => $param['admin_id'],
						'create_time' => time(),
					);
					Db::name('CustomerLog')->strict(false)->field(true)->insert($log_data);
					return to_assign();
				} else {
					return to_assign(1, '操作失败');
				}                
            }
        } else {
            $customer_id = isset($param['cid']) ? $param['cid'] : 0;
            $id = isset($param['id']) ? $param['id'] : 0;
			if ($id > 0) {
				View::assign('detail', (new CustomerChance())->detail($id));
				return view('chance_edit');
			}
			$customer_name = Db::name('Customer')->where('id',$customer_id)->value('name');
            View::assign('customer_id', $customer_id);
            View::assign('customer_name', $customer_name);
            return view();
        }
    }
	
	
	//查看销售机会
	public function chance_view()
    {
		$param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		$detail = (new CustomerChance())->detail($id);
		if(empty($detail)){
			echo '<div style="text-align:center;color:red;margin-top:20%;">找不到该销售机会</div>';exit;
		}
		View::assign('detail',$detail);
        return view();
    }
	
	//删除销售机会
	public function chance_del()
    {
        if (request()->isDelete()) {
			$param = get_params();
			$admin_id = Db::name('CustomerChance')->where(['id' => $param['id']])->value('admin_id');
			if($admin_id != $this->uid){
				return to_assign(1, '你不是该跟销售机会的创建人，无权限删除');
			}
            $param['delete_time'] = time();
			$res = CustomerChance::strict(false)->field(true)->update($param);
			if ($res) {
				add_log('delete', $param['id'], $param,'客户销售机会');
				to_log($this->uid,3,$param,['delete_time'=>0]);
				return to_assign();
			} else {
				return to_assign(1, '操作失败');
			}
        } else {
           return to_assign(1, '参数错误');
        }
    }
   
   
}
