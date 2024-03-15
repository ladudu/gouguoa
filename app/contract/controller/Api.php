<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\contract\controller;

use app\api\BaseController;
use app\contract\model\ContractLog;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	//获取合同协议
	public function get_contract()
    {
        $param = get_params();
		$where = array();
		$whereOr = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|name', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$where[] = ['check_status', '=', 2];
		$uid = $this->uid;
		$auth = isAuth($uid,'contract_admin');
		if($auth==0){
			$whereOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			$dids = get_department_role($this->uid);
			if(!empty($dids)){
				$whereOr[] =['sign_did', 'in', $dids];
			}
		}
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('Contract')
			->field('id,name,code,customer_id,sign_uid,sign_time')
			->order('id desc')
			->where($where)
			->where(function ($query) use($whereOr) {
					$query->whereOr($whereOr);
			})
			->paginate($rows, false)->each(function($item, $key){
				$item['sign_name'] = Db::name('Admin')->where('id',$item['sign_uid'])->value('name');
                $item['sign_time'] = date('Y-m-d', $item['sign_time']);
                $item['customer'] =  Db::name('Customer')->where('id',$item['customer_id'])->value('name');
				return $item;
			});
        table_assign(0, '', $list);
    }

    //添加附件
    public function add_file()
    {
        $param = get_params();
        $param['create_time'] = time();
        $param['admin_id'] = $this->uid;
        $fid = Db::name('ContractFile')->strict(false)->field(true)->insertGetId($param);
        if ($fid) {
            $log_data = array(
                'field' => 'file',
                'action' => 'upload',
                'contract_id' => $param['contract_id'],
                'admin_id' => $param['admin_id'],
                'old_content' => '',
                'new_content' => $param['file_name'],
                'create_time' => time(),
            );
            Db::name('ContractLog')->strict(false)->field(true)->insert($log_data);
            return to_assign(0, '上传成功', $fid);
        }
    }
    
    //删除
    public function delete_file()
    {
        if (request()->isDelete()) {
			$id = get_params("id");
			$data['id'] = $id;
			$data['delete_time'] = time();
			if (Db::name('ContractFile')->update($data) !== false) {
				$detail = Db::name('ContractFile')->where('id', $id)->find();
				$file_name = Db::name('File')->where('id', $detail['file_id'])->value('name');
                $log_data = array(
                    'field' => 'file',
                    'action' => 'delete',
                    'contract_id' => $detail['contract_id'],
                    'admin_id' => $this->uid,
                    'new_content' => $file_name,
                    'create_time' => time(),
                );
                Db::name('ContractLog')->strict(false)->field(true)->insert($log_data);
				return to_assign(0, "删除成功");
			} else {
				return to_assign(1, "删除失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    } 

	//状态改变等操作
    public function check()
    {
        if (request()->isPost()) {
			$param = get_params();
			if($param['check_status'] == 0){
				$param['check_step_sort'] = 0;
			}
			if($param['check_status'] == 1){
				$check_admin_ids = isset($param['check_admin_ids'])?$param['check_admin_ids']:'';
				$flow_data = set_flow($param['flow_id'],$check_admin_ids,$this->uid);
				$param['check_admin_ids'] = $flow_data['check_admin_ids'];
				$flow = $flow_data['flow'];
				$check_type = $flow_data['check_type'];
				//删除原来的审核流程和审核记录
				Db::name('FlowStep')->where(['action_id'=>$param['id'],'type'=>4,'delete_time'=>0])->update(['delete_time'=>time()]);
				Db::name('FlowRecord')->where(['action_id'=>$param['id'],'type'=>4,'delete_time'=>0])->update(['delete_time'=>time()]);	
				if($check_type == 2){
					$flow_step = array(
						'action_id' => $param['id'],
						'type' => 4,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}
				else{
					foreach ($flow as $key => &$value){
						$value['action_id'] = $param['id'];
						$value['sort'] = $key;
						$value['type'] = 4;
						$value['create_time'] = time();
					}
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				}
				$checkData=array(
					'action_id' => $param['id'],
					'step_id' => 0,
					'check_user_id' => $this->uid,
					'type' => 4,
					'check_time' => time(),
					'status' => 0,
					'content' => '提交申请',
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				//发送消息通知
				$msg=[
					'from_uid'=>$this->uid,
					'title'=>'合同',
					'action_id'=>$param['id']
				];
				$users = $param['check_admin_ids'];
				sendMessage($users,51,$msg);
			}
			if($param['check_status'] == 3){
				$param['check_uid'] = $this->uid;
				$param['check_time'] = time();
				$param['check_remark'] = $param['mark'];
			}
			if($param['check_status'] == 5){
				$param['stop_uid'] = $this->uid;
				$param['stop_time'] = time();
				$param['stop_remark'] = $param['mark'];
			}
			if($param['check_status'] == 6){
				$param['void_uid'] = $this->uid;
				$param['void_time'] = time();
				$param['void_remark'] = $param['mark'];
			}
			$old =  Db::name('Contract')->where('id', $param['id'])->find();
			if (Db::name('Contract')->strict(false)->update($param) !== false) {
                $log_data = array(
                    'field' => 'check_status',
                    'contract_id' => $param['id'],
                    'admin_id' => $this->uid,
                    'new_content' => $param['check_status'],
                    'old_content' => $old['check_status'],
                    'create_time' => time(),
                );
                Db::name('ContractLog')->strict(false)->field(true)->insert($log_data);
				return to_assign(0, "操作成功");
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }

	//归档等操作
    public function archive()
    {
        if (request()->isPost()) {
			$param = get_params();
			$old = 1;
			if($param['archive_status'] == 1){
				$param['archive_uid'] = $this->uid;
				$param['archive_time'] = time();
				$old = 0;
			}
			$old =  Db::name('Contract')->where('id', $param['id'])->find();
			if (Db::name('Contract')->strict(false)->update($param) !== false) {
                $log_data = array(
                    'field' => 'archive_status',
                    'contract_id' => $param['id'],
                    'admin_id' => $this->uid,
                    'new_content' => $param['archive_status'],
                    'old_content' => $old['archive_status'],
                    'create_time' => time(),
                );
                Db::name('ContractLog')->strict(false)->field(true)->insert($log_data);
				return to_assign(0, "操作成功");
			} else {
				return to_assign(1, "操作失败");
			}
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//合同操作日志列表
    public function contract_log()
    {
		$param = get_params();
		$list = new ContractLog();
		$content = $list->contract_log($param);
		return to_assign(0, '', $content);
    }
	
	//获取客户列表
	public function get_customer()
    {
        $param = get_params();
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['id|name', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$uid = $this->uid;
		$auth = isAuth($uid,'customer_admin');
		$dids = get_department_role($this->uid);
		if($auth==0){
			$whereOr[] =['belong_uid', '=', $uid];	
			if(!empty($dids)){
				$whereOr[] =['belong_did', 'in', $dids];
			}			
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
		}
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('Customer')->field('id,name,address')->order('id asc')->where($where)->paginate($rows, false)->each(function($item, $key){
			$contact = Db::name('CustomerContact')->where(['cid'=>$item['id'],'is_default'=>1])->find();
			if(!empty($contact)){
				$item['contact_name'] = $contact['name'];
				$item['contact_mobile'] = $contact['mobile'];
			}
			else{
				$item['contact_name'] = '';
				$item['contact_mobile'] = '';
			}
			return $item;
		});
        table_assign(0, '', $list);
    }

}
