<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\customer\controller;

use app\api\BaseController;
use app\customer\model\CustomerTrace;
use app\customer\model\CustomerContact;
use app\customer\model\CustomerChance;
use app\customer\model\CustomerLog;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	
	//分配客户
	public function distribute()
    {
		if (request()->isAjax()) {
			$params = get_params();
			//是否是客户管理员
			$auth = isAuth($this->uid,'customer_admin');
			if($auth==0){
				return to_assign(1, "只有客户管理员才有权限操作");
			}
			$data['id'] = $params['id'];
			$data['belong_uid'] = $params['uid'];
			$data['belong_did'] = $params['did'];
			$data['distribute_time'] = time();
			if (Db::name('Customer')->update($data) !== false) {
				add_log('allot', $data['id'],[],'客户');
				to_log($this->uid,0,$data,['belong_uid'=>0]);
				return to_assign(0, "操作成功");
			} else {
				return to_assign(1, "操作失败");
			}
		} else {
            return to_assign(1, "错误的请求");
        }
	}
	
	//跟进记录列表
	public function get_trace()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['cid', '=', $param['customer_id']];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$content = CustomerTrace::where($where)
			->order('create_time desc')
            ->paginate($rows, false, ['query' => $param])
			->each(function ($item, $key) {
				$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
				$item->create_time = date('Y-m-d H:i:s', (int) $item->create_time);
				$item->follow_time = date('Y-m-d H:i', (int) $item->follow_time);
				$item->next_time = date('Y-m-d H:i', (int) $item->next_time);
				$item->stage_name = CustomerTrace::$Stage[(int) $item->stage];
				$item->type_name = CustomerTrace::$Type[(int) $item->type];
			});
		return table_assign(0, '', $content);
    }
	
	//添加跟进记录
	public function add_trace()
    {
		$param = get_params();
        if (request()->isAjax()) {
			if(isset($param['follow_time'])){
				$param['follow_time'] = strtotime($param['follow_time']);
			}
			if(isset($param['next_time'])){
				$param['next_time'] = strtotime($param['next_time']);
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
				$old = CustomerTrace::where(['id' => $param['id']])->find();
				if($this->uid!=$old['admin_id'] && get_user_role($this->uid,$old['admin_id'])==0){
					return to_assign(1, "只有所属员工才有权限操作");
				}
				$res = CustomerTrace::strict(false)->field(true)->update($param);
				if ($res) {
					add_log('edit', $param['id'], $param,'客户跟进记录');
					to_log($this->uid,1,$param,$old);
					return to_assign();
				} else {
					return to_assign(1, '操作失败');
				}
            } else {
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
				$tid = CustomerTrace::strict(false)->field(true)->insertGetId($param);
				if ($tid) {
					if(!empty($param['chance_id'])){
						Db::name('CustomerChance')->where('id',$param['chance_id'])->update(['stage'=>$param['stage']]);
					}
					add_log('add', $tid, $param,'客户跟进记录');
					$log_data = array(
						'field' => 'new',
						'action' => 'add',
						'type' => 1,
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
				View::assign('detail', (new CustomerTrace())->detail($id));
				return view('edit_trace');
			}
			$customer_name = Db::name('Customer')->where('id',$customer_id)->value('name');
            View::assign('customer_id', $customer_id);
            View::assign('customer_name', $customer_name);
            return view();
        }
    }
	
	
	//查看跟进记录
	public function view_trace()
    {
		$param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		$detail = (new CustomerTrace())->detail($id);
		if(empty($detail)){
			echo '<div style="text-align:center;color:red;margin-top:20%;">找不到该跟进记录</div>';exit;
		}
		View::assign('detail',$detail);
        return view();
    }
	
	//删除跟进记录
	public function delete_trace()
    {
        if (request()->isDelete()) {
			$param = get_params();
			$admin_id = Db::name('CustomerTrace')->where(['id' => $param['id']])->value('admin_id');
			if($admin_id != $this->uid){
				return to_assign(1, '你不是该跟进记录的创建人，无权限删除');
			}
            $param['delete_time'] = time();
			$res = CustomerTrace::strict(false)->field(true)->update($param);
			if ($res) {
				add_log('delete', $param['id'], $param,'客户跟进记录');
				to_log($this->uid,1,$param,['delete_time'=>0]);
				return to_assign();
			} else {
				return to_assign(1, '操作失败');
			}
        } else {
           return to_assign(1, '参数错误');
        }
    }
	
	
	//销售机会列表
	public function get_chance()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['cid', '=', $param['customer_id']];
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$content = CustomerChance::where($where)
			->order('create_time desc')
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
    }
	
	//添加销售机会
	public function add_chance()
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
					return to_assign(1, "只有所属员工才有权限操作");
				}
				$res = CustomerChance::strict(false)->field(true)->update($param);
				if ($res) {
					add_log('edit', $param['id'], $param,'客户销售机会');
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
				return view('edit_chance');
			}
			$customer_name = Db::name('Customer')->where('id',$customer_id)->value('name');
            View::assign('customer_id', $customer_id);
            View::assign('customer_name', $customer_name);
            return view();
        }
    }
	
	
	//添加销售机会
	public function view_chance()
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
	public function delete_chance()
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
	
	
	//获取联系人数据
	public function get_contact()
    {
		$param = get_params();
		$where = array();
		$where[] = ['delete_time', '=', 0];
		$where[] = ['cid', '=', $param['customer_id']];
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$content = CustomerContact::where($where)
			->order('create_time desc')
            ->paginate($rows, false, ['query' => $param])
			->each(function ($item, $key) {					
				$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
				$item->create_time = date('Y-m-d H:i:s', (int) $item->create_time);
			});
		return table_assign(0, '', $content);
    }
	
	//设置联系人
	public function set_contact()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$detail= Db::name('CustomerContact')->where(['id' => $param['id']])->find();
			CustomerContact::where(['cid' => $detail['cid']])->strict(false)->field(true)->update(['is_default'=>0]);
			$res = CustomerContact::where(['id' => $param['id']])->update(['is_default'=>1]);
			if ($res) {
				add_log('edit', $param['id'], $param,'客户联系人');
				to_log($this->uid,2,$param,$detail);
				return to_assign();
			} else {
				return to_assign(1, '操作失败');
			}
        } else {
           return to_assign(1, '参数错误');
        }
    }
	

    //添加附件
    public function add_file()
    {
        $param = get_params();
        $param['create_time'] = time();
        $param['admin_id'] = $this->uid;
        $fid = Db::name('CustomerFile')->strict(false)->field(true)->insertGetId($param);
        if ($fid) {
            $log_data = array(
                'field' => 'file',
                'action' => 'upload',
                'customer_id' => $param['customer_id'],
                'admin_id' => $param['admin_id'],
                'old_content' => '',
                'new_content' => $param['file_name'],
                'create_time' => time(),
            );
            Db::name('CustomerLog')->strict(false)->field(true)->insert($log_data);
            return to_assign(0, '上传成功', $fid);
        }
    }
    
    //删除附件
    public function delete_file()
    {
        if (request()->isDelete()) {
			$id = get_params("id");
			$data['id'] = $id;
			$data['delete_time'] = time();
			if (Db::name('CustomerFile')->update($data) !== false) {
				$detail = Db::name('CustomerFile')->where('id', $id)->find();
				$file_name = Db::name('File')->where('id', $detail['file_id'])->value('name');
                $log_data = array(
                    'field' => 'file',
                    'action' => 'delete',
                    'customer_id' => $detail['customer_id'],
                    'admin_id' => $this->uid,
                    'new_content' => $file_name,
                    'create_time' => time(),
                );
                Db::name('CustomerLog')->strict(false)->field(true)->insert($log_data);
				return to_assign(0, "删除成功");
			} else {
				return to_assign(1, "删除失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//操作日志列表
    public function customer_log()
    {
		$param = get_params();
		$list = new CustomerLog();
		$content = $list->customer_log($param);
		return to_assign(0, '', $content);
    }

}
