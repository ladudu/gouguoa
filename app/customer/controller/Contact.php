<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\customer\controller;

use app\base\BaseController;
use app\customer\model\CustomerContact;
use app\customer\validate\CustomerContactCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Contact extends BaseController
{	
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.name|a.mobile|c.name', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['a.delete_time', '=', 0];
			
			$uid = $this->uid;
			$auth = isAuth($uid,'customer_admin');
			if($auth==0){
				$dids = get_department_role($this->uid);
				if(!empty($dids)){
					$whereOr[] =['c.belong_did', 'in', $dids];
				}
				$whereOr[] =['c.belong_uid', '=', $uid];				
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',c.share_ids)")];
			}
			
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $content = CustomerContact::where($where)
				->where(function ($query) use($whereOr) {
					$query->whereOr($whereOr);
				})
                ->field('a.*,c.name as customer')
                ->alias('a')
                ->join('customer c', 'a.cid = c.id')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param])
				->each(function ($item, $key) {
                    $item->create_time = date('Y-m-d H:i:s', (int) $item->create_time);
				});
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }
    //添加
    public function contact_add()
    {
		$param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(CustomerContactCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$detail= Db::name('CustomerContact')->where(['id' => $param['id']])->find();
                $param['update_time'] = time();
                $res = Db::name('CustomerContact')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
					to_log($this->uid,2,$param,$detail);
                }
                return to_assign();
            } else {
                try {
                    validate(CustomerContactCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$count= Db::name('CustomerContact')->where(['cid' => $param['cid'],'delete_time' => 0])->count();
				if($count == 0){
					$param['is_default'] = 1;	
				}
                $param['admin_id'] = $this->uid;
                $param['create_time'] = time();
                $insertId = Db::name('CustomerContact')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
					$log_data = array(
						'field' => 'new',
						'action' => 'add',
						'type' => 2,
						'customer_id' => $insertId,
						'admin_id' => $param['admin_id'],
						'create_time' => time(),
					);
					Db::name('CustomerLog')->strict(false)->field(true)->insert($log_data);
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $customer_id = isset($param['cid']) ? $param['cid'] : 0;
            $id = isset($param['id']) ? $param['id'] : 0;
			if ($id > 0) {
				View::assign('detail', (new CustomerContact())->detail($id));
				return view('contact_edit');
			}
			$customer_name = Db::name('Customer')->where('id',$customer_id)->value('name');
            View::assign('customer_id', $customer_id);
            View::assign('customer_name', $customer_name);
            return view();
        }
	}
	
    //设置
    public function contact_del()
    {
		if (request()->isDelete()) {
			$param = get_params();
			$contact = Db::name('CustomerContact')->where(['id' => $param['id']])->find();
			if($contact['is_default'] == 1){
				return to_assign(1, '客户的首要联系人不能删除');
			}
			if($contact['admin_id'] != $this->uid){
				return to_assign(1, '你不是该联系人的创建人，无权限删除');
			}
            $param['delete_time'] = time();
			$res = CustomerContact::strict(false)->field(true)->update($param);
			if ($res) {
				add_log('edit', $param['id'], $param);
				to_log($this->uid,2,$param,['delete_time'=>0]);
				return to_assign();
			} else {
				return to_assign(1, '操作失败');
			}
        } else {
           return to_assign(1, '参数错误');
        }
    }  
   
   
}
