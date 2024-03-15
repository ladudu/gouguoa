<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\Invoice as InvoiceList;
use app\finance\validate\InvoiceCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Invoice extends BaseController
{
	//我申请的发票
    public function index()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['check_status'])) {
				$where[] = ['i.check_status','=',$param['check_status']];
            }
            //按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['i.create_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
			}		
			$where[] = ['i.admin_id','=',$this->uid];
			$where[] = ['i.delete_time','=',0];
			$model = new InvoiceList();
            $list = $model->get_list($param, $where);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//待审批的发票
    public function list()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$status = isset($param['status'])?$param['status']:0;
			$user_id = $this->uid;
			//查询条件
			$map1 = [];
			$map2 = [];
			$map1[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',i.check_admin_ids)")];
			$map1[] = ['i.delete_time','=',0];
			$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',i.flow_admin_ids)")];
			$map2[] = ['i.delete_time','=',0];
			$model = new InvoiceList();
			if($status == 0){
				$list = $model->get_list($param,[$map1,$map2],'or');
			}			
			if($status == 1){
				$list = $model->get_list($param,$map1);
			}
			if($status == 2){
				$list = $model->get_list($param,$map2);
            }	
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	public function copy()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$user_id = $this->uid;
			//查询条件
			$map = [];
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$map[] = ['i.create_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
			}
			$map[] = ['i.delete_time','=',0];
			$map[] = ['i.check_status', '=', 2];			
			$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',i.copy_uids)")];
			$model = new InvoiceList();
			$list = $model->get_list($param,$map);			
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//发票开具
	public function checkedlist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['check_status'])) {
				$where[] = ['i.check_status','=',$param['check_status']];
            }
			else{
				$where[] = ['i.check_status','in',[2,5,10]];
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['i.create_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
			}			
			$where[] = ['i.delete_time','=',0];
			$model = new InvoiceList();
			$list = $model->get_list($param,$where);
            return table_assign(0, '', $list);
        } else {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return view('../../base/view/common/roletemplate');
			}
            return view();
        }
    }

    //添加&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['check_status'] = 1;
			$param['check_step_sort'] = 0;
			$flow_list = Db::name('Flow')->where('id',$param['flow_id'])->value('flow_list');
			$flow = unserialize($flow_list);
            if ($param['type'] == 1) {
                if (!$param['invoice_tax']) {
                    return to_assign(1, '纳税人识别号不能为空');
                }
                if (!$param['invoice_bank']) {
                    return to_assign(1, '开户银行不能为空');
                }
                if (!$param['invoice_account']) {
                    return to_assign(1, '银行账号不能为空');
                }
                if (!$param['invoice_banking']) {
                    return to_assign(1, '银行营业网点不能为空');
                }
                if (!$param['invoice_address']) {
                    return to_assign(1, '银行地址不能为空');
                }
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(InvoiceCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
				
				//删除原来的审核流程和审核记录
				Db::name('FlowStep')->where(['action_id'=>$param['id'],'type'=>3,'delete_time'=>0])->update(['delete_time'=>time()]);
				Db::name('FlowRecord')->where(['action_id'=>$param['id'],'type'=>3,'delete_time'=>0])->update(['delete_time'=>time()]);		
				if (!isset($param['check_admin_ids'])) {
					if($flow[0]['flow_type'] == 1){
						//部门负责人
						$leader = get_department_leader($this->uid);
						if($leader == 0){
							return to_assign(1,'审批流程设置有问题：当前部门负责人还未设置，请联系HR或者管理员');
						}
						else{
							$param['check_admin_ids'] = $leader;
						}						
					}
					else if($flow[0]['flow_type'] == 2){
						//上级部门负责人
						$leader = get_department_leader($this->uid,1);
						if($leader == 0){
							return to_assign(1,'审批流程设置有问题：上级部门负责人还未设置，请联系HR或者管理员');
						}
						else{
							$param['check_admin_ids'] = $leader;
						}
					}
					else{
						$param['check_admin_ids'] = $flow[0]['flow_uids'];
					}
					foreach ($flow as $key => &$value){
						$value['action_id'] = $param['id'];
						$value['sort'] = $key;
						$value['type'] = 3;
						$value['create_time'] = time();
					}
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				}
				else{
					$flow_step = array(
						'action_id' => $param['id'],
						'type' => 3,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}
				
                $res = InvoiceList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res !== false) {
					//添加提交申请记录
					$checkData=array(
						'action_id' => $param['id'],
						'check_user_id' => $this->uid,
						'content' => '重新提交申请',
						'type' => 3,
						'check_time' => time(),
						'create_time' => time()
					);	
					$record_id = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
                    add_log('edit', $param['id'], $param);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,
						'title'=>'发票',
						'action_id'=>$param['id']
					];
					$users = $param['check_admin_ids'];
					sendMessage($users,41,$msg);
                    return to_assign();   
                } else {
                    return to_assign(1, '操作失败');
                }
            } else {
                try {
                    validate(InvoiceCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['admin_id'] = $this->uid;
                $param['did'] = $this->did;
                $param['create_time'] = time();
				
				if (!isset($param['check_admin_ids'])) {
					if($flow[0]['flow_type'] == 1){
						//部门负责人
						$leader = get_department_leader($this->uid);
						if($leader == 0){
							return to_assign(1,'审批流程设置有问题：当前部门负责人还未设置，请联系HR或者管理员');
						}
						else{
							$param['check_admin_ids'] = $leader;
						}						
					}
					else if($flow[0]['flow_type'] == 2){
						//上级部门负责人
						$leader = get_department_leader($this->uid,1);
						if($leader == 0){
							return to_assign(1,'审批流程设置有问题：上级部门负责人还未设置，请联系HR或者管理员');
						}
						else{
							$param['check_admin_ids'] = $leader;
						}
					}
					else{
						$param['check_admin_ids'] = $flow[0]['flow_uids'];
					}
					$exid = InvoiceList::strict(false)->field(true)->insertGetId($param);
					foreach ($flow as $key => &$value){
						$value['action_id'] = $exid;
						$value['sort'] = $key;
						$value['type'] = 3;
						$value['create_time'] = time();
					}
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				}
				else{
					$exid = InvoiceList::strict(false)->field(true)->insertGetId($param);
					$flow_step = array(
						'action_id' => $exid,
						'type' => 3,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					//增加审核流程
					Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}				
                
                if ($exid) {
					//添加提交申请记录
					$checkData=array(
						'action_id' => $exid,
						'check_user_id' => $this->uid,
						'content' => '提交申请',
						'type' => 3,
						'check_time' => time(),
						'create_time' => time()
					);	
					$record_id = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
                    add_log('apply', $exid, $param);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,
						'title'=>'发票',
						'action_id'=>$exid
					];
					$users = $param['check_admin_ids'];
					sendMessage($users,41,$msg);
                    return to_assign();   
                } else {
                     return to_assign(1, '操作失败');
                }
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $model = new InvoiceList();
				$detail = $model->detail($id);
				if(empty($detail)){
					throw new \think\exception\HttpException(406, '找不到记录');
				}
				if($detail['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['fileArray'] = $fileArray;
				}
                View::assign('detail', $detail);
            }
			$department = $this->did;
			//获取发票审批流程
			$flows = get_type_department_flows(7,$department);
            View::assign('user', get_admin($this->uid));
            View::assign('id', $id);
			View::assign('flows', $flows);
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
		$model = new InvoiceList();
		$detail = $model->detail($id);
		if(empty($detail)){
			throw new \think\exception\HttpException(406, '找不到记录');
		}
		$flows = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>3,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		$detail['check_user'] = '-';
		$detail['copy_user'] = '-';
		$check_user_ids = [];
		if($detail['check_status'] == 1){
			if($flows['flow_type']==1){
				$detail['check_user'] = '部门负责人';				
				$check_user_ids[]=get_department_leader($detail['admin_id']);
			}
			else if($flows['flow_type']==2){
				$detail['check_user'] = '上级部门负责人';
				$check_user_ids[]=get_department_leader($detail['admin_id'],1);
			}
			else{
				$check_user_ids = explode(',',$flows['flow_uids']);
				$check_user = Db::name('Admin')->where('id','in',$flows['flow_uids'])->column('name');
				$detail['check_user'] = implode(',',$check_user);			
			}
		}
		
		if($detail['copy_uids'] !=''){
			$copy_user = Db::name('Admin')->where('id','in',$detail['copy_uids'])->column('name');
			$detail['copy_user'] = implode(',',$copy_user);
		}
		if($detail['file_ids'] !=''){
			$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['fileArray'] = $fileArray;
		}
		
		if($detail['other_file_ids'] !=''){
			$fileArrayOther = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
			$detail['fileArrayOther'] = $fileArrayOther;
		}

		$is_check_admin = 0;
		$is_create_admin = 0;
		if($detail['admin_id'] == $this->uid){
			$is_create_admin = 1;
		}
		if(in_array($this->uid,$check_user_ids)){
			$is_check_admin = 1;
			//当前审核节点详情
			if($flows['flow_type'] == 4){
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>3,'step_id'=>$flows['id'],'check_user_id'=>$this->uid])->count();
				if($check_count>0){
					$is_check_admin = 0;
				}
			}
		}
		
		$check_record = Db::name('FlowRecord')->field('f.*,a.name,a.thumb')
			->alias('f')
			->join('Admin a', 'a.id = f.check_user_id', 'left')
			->where(['f.action_id'=>$detail['id'],'f.type'=>3])
			->order('check_time desc')
			->select()->toArray();
		foreach ($check_record as $kk => &$vv) {		
			$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
			$vv['status_str'] = '提交';
			if($vv['status'] == 1){
				$vv['status_str'] = '审核通过';
			}
			else if($vv['status'] == 2){
				$vv['status_str'] = '审核拒绝';
			}
			if($vv['status'] == 3){
				$vv['status_str'] = '撤销';
			}
		}
		
		View::assign('is_create_admin', $is_create_admin);
		View::assign('is_check_admin', $is_check_admin);
		View::assign('check_record', $check_record);
		View::assign('detail', $detail);
		View::assign('flows', $flows);
        View::assign('uid', $this->uid);
        return view();
    }

    //删除
    public function delete()
    {
        $id = get_params("id");
        $status = InvoiceList::where(['id' => $id])->value('check_status');
        if ($status == 2) {
            return to_assign(1, "已审核的发票不能删除");
        }
        if ($status == 3) {
            return to_assign(1, "已开具的发票不能删除");
        }
        $data['delete_time'] = time();
        $data['id'] = $id;
        if (InvoiceList::update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }	
}
