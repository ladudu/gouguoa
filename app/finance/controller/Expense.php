<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\Expense as ExpenseList;
use app\finance\validate\ExpenseCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Expense extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];
            $where[] = ['delete_time', '=', 0];
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['expense_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
			}

			$where[] = ['admin_id','=',$this->uid];
            if (!empty($param['check_status']) && $param['check_status']!='') {
                $where[] = ['check_status', '=', $param['check_status']];
            }    
			$model = new ExpenseList;
            $list = $model->get_list($param,$where);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//待审批的报销
    public function list()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$status = isset($param['status'])?$param['status']:0;
			$user_id = $this->uid;
			//查询条件
			$map1 = [];
			$map2 = [];
			$map1[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',check_admin_ids)")];
			$map1[] = ['delete_time', '=', 0];
			
			$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',flow_admin_ids)")];
			$map2[] = ['delete_time', '=', 0];
			
			if($status == 0){
				$model = new ExpenseList;
				$list = $model->get_list($param,[$map1,$map2],'or');
			}			
			if($status == 1){
				$model = new ExpenseList;
				$list = $model->get_list($param,$map1);
			}
			if($status == 2){
				$model = new ExpenseList;
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
				$map[] = ['expense_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
			}	
			$map[] = ['check_status', 'in', [2,3,5]];			
			$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',copy_uids)")];
			$model = new ExpenseList;
			$list = $model->get_list($param,$map);		
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//报销打款
	public function checkedlist()
    {
        if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (!empty($param['check_status'])) {
				$where[] = ['check_status','=',$param['check_status']];
            }
			else{
				$where[] = ['check_status','in',[2,5]];
			}
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['expense_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
			}		
			$model = new ExpenseList;
			$list = $model->get_list($param,$where);	
            return table_assign(0, '', $list);
        } else {
			$auth = isAuthExpense($this->uid);
			if($auth == 0){
				return view('../../base/view/common/roletemplate');
			}
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $admin_id = $this->uid;       
            $param['income_month'] = isset($param['income_month']) ? strtotime(urldecode($param['income_month'])) : 0;
            $param['expense_time'] = isset($param['expense_time']) ? strtotime(urldecode($param['expense_time'])) : 0;
            $param['check_status'] = 1;
			$param['check_step_sort'] = 0;
			
			$amountData = isset($param['amount']) ? $param['amount'] : '0';
			if ($amountData == 0) {
				return to_assign(1,'报销金额不完善');
			}
			else{
				foreach ($amountData as $key => $value) {
					if ($value == 0) {
						return to_assign(1,'第' . ($key + 1) . '条报销金额不能为零');
					}
				}
			}
			
			$flow_list = Db::name('Flow')->where('id',$param['flow_id'])->value('flow_list');
			$flow = unserialize($flow_list);	
			if (!isset($param['check_admin_ids'])) {
				if($flow[0]['flow_type'] == 1){
					//部门负责人
					$leader = get_department_leader($admin_id);
					if($leader == 0){
						return to_assign(1,'审批流程设置有问题：当前部门负责人还未设置，请联系HR或者管理员');
					}					
				}
				else if($flow[0]['flow_type'] == 2){
					//上级部门负责人
					$leader = get_department_leader($admin_id,1);
					if($leader == 0){
						return to_assign(1,'审批流程设置有问题：上级部门负责人还未设置，请联系HR或者管理员');
					}
				}
			}
			
			$dbRes = false; 
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ExpenseCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                Db::startTrans();
                try {
					//删除原来的审核流程和审核记录
					Db::name('FlowStep')->where(['action_id'=>$param['id'],'type'=>2,'delete_time'=>0])->update(['delete_time'=>time()]);
					Db::name('FlowRecord')->where(['action_id'=>$param['id'],'type'=>2,'delete_time'=>0])->update(['delete_time'=>time()]);		
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
							$value['type'] = 2;
							$value['create_time'] = time();
						}
						//增加审核流程
						Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
					}
					else{
						$flow_step = array(
							'action_id' => $param['id'],
							'type' => 2,
							'flow_uids' => $param['check_admin_ids'],
							'create_time' => time()
						);
						//增加审核流程
						Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
					}
					
                    $res = ExpenseList::where('id', $param['id'])->strict(false)->field(true)->update($param);
					
					$exid = $param['id'];
					//相关内容多个数组;
					$amountData = isset($param['amount']) ? $param['amount'] : '';
					$remarksData = isset($param['remarks']) ? $param['remarks'] : '';
					$cateData = isset($param['cate_id']) ? $param['cate_id'] : '';
					$idData = isset($param['expense_id']) ? $param['expense_id'] : 0;
					if ($amountData) {
						foreach ($amountData as $key => $value) {
							if (!$value) {
								continue;
							}    
							$data = [];
							$data['id'] = $idData[$key];
							$data['exid'] = $exid;
							$data['admin_id'] = $admin_id;
							$data['amount'] = $amountData[$key];
							$data['cate_id'] = $cateData[$key];
							$data['remarks'] = $remarksData[$key];
							if ($data['id'] > 0) {
								$data['update_time'] = time();
								$resa = Db::name('ExpenseInterfix')->strict(false)->field(true)->update($data);
							} else {
								$data['create_time'] = time();
								$eid = Db::name('ExpenseInterfix')->strict(false)->field(true)->insertGetId($data);
							}
						}
					}		
					//添加提交申请记录
					$checkData=array(
						'action_id' => $exid,
						'check_user_id' => $this->uid,
						'content' => '重新提交申请',
						'type' => 2,
						'check_time' => time(),
						'create_time' => time()
					);	
					$record_id = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);	
					add_log('edit', $exid, $param);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,
						'title'=>'报销',
						'action_id'=>$param['id']
					];
					$users = $param['check_admin_ids'];
					sendMessage($users,31,$msg);
					Db::commit();
					$dbRes = true;
                } catch (\Exception $e) { ##这里参数不能删除($e：错误信息)
					Db::rollback();
                    return to_assign(1, $e->getMessage());
                }
            } else {
                try {
                    validate(ExpenseCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $admin_id;
                $param['did'] = $this->did;
                Db::startTrans();
                try {
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
						$exid = ExpenseList::strict(false)->field(true)->insertGetId($param);
						foreach ($flow as $key => &$value){
							$value['action_id'] = $exid;
							$value['sort'] = $key;
							$value['type'] = 2;
							$value['create_time'] = time();
						}
						//增加审核流程
						Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
					}
					else{
						$exid = ExpenseList::strict(false)->field(true)->insertGetId($param);
						$flow_step = array(
							'action_id' => $exid,
							'type' => 2,
							'flow_uids' => $param['check_admin_ids'],
							'create_time' => time()
						);
						//增加审核流程
						Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
					}
					
					//相关内容多个数组;
					$amountData = isset($param['amount']) ? $param['amount'] : '';
					$remarksData = isset($param['remarks']) ? $param['remarks'] : '';
					$cateData = isset($param['cate_id']) ? $param['cate_id'] : '';
					if ($amountData) {
						foreach ($amountData as $key => $value) {
							if (!$value) {
								continue;
							}
							$data = [];
							$data['exid'] = $exid;
							$data['admin_id'] = $admin_id;
							$data['amount'] = $amountData[$key];
							$data['cate_id'] = $cateData[$key];
							$data['remarks'] = $remarksData[$key];
							$data['create_time'] = time();
							$eid = Db::name('ExpenseInterfix')->strict(false)->field(true)->insertGetId($data);
						}
					}
					//添加提交申请记录
					$checkData=array(
						'action_id' => $exid,
						'check_user_id' => $this->uid,
						'content' => '提交申请',
						'type' => 2,
						'check_time' => time(),
						'create_time' => time()
					);	
					$record_id = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);					
					add_log('add', $exid, $param);
					//发送消息通知
					$msg=[
						'from_uid'=>$this->uid,
						'title'=>'报销',
						'action_id'=>$exid
					];
					$users = $param['check_admin_ids'];
					sendMessage($users,31,$msg);
					Db::commit();
					$dbRes = true;
                } catch (\Exception $e) { ##这里参数不能删除($e：错误信息)
					Db::rollback();
                    return to_assign(1, $e->getMessage());
                }
            }
            if ($dbRes == true) {
                return to_assign();
            } else {
                return to_assign(1, '保存失败');
            }
        }
        else{
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $expense = (new ExpenseList())->detail($id);
				if($expense['admin_id']!=$this->uid){
					throw new \think\exception\HttpException(403, '禁止访问');
				}
				if($expense['check_status']!=4){
					throw new \think\exception\HttpException(403, '禁止访问');
				}
				if($expense['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$expense['file_ids'])->select();
					$expense['fileArray'] = $fileArray;
				}
                View::assign('expense', $expense);
            }
			$department = $this->did;
			//获取报销审批流程
			$flows = get_type_department_flows(6,$department);
            $expense_cate = Db::name('ExpenseCate')->where(['status' => 1])->select()->toArray();
            View::assign('user', get_admin($this->uid));
            View::assign('expense_cate', $expense_cate);
            View::assign('flows', $flows);
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $detail = (new ExpenseList())->detail($id);
		$flows = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>2,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		$detail['check_user'] = '-';
		$detail['copy_user'] = '-';
		$check_user_ids = [];
		if($detail['check_status']==1){
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
		
		$is_check_admin = 0;
		$is_create_admin = 0;
		if($detail['admin_id'] == $this->uid){
			$is_create_admin = 1;
		}
		if(in_array($this->uid,$check_user_ids)){
			$is_check_admin = 1;
			//当前审核节点详情
			$step = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>2,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
			if($step['flow_type'] == 4){
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>2,'step_id'=>$step['id'],'check_user_id'=>$this->uid])->count();
				if($check_count>0){
					$is_check_admin = 0;
				}
			}
		}
		
		$check_record = Db::name('FlowRecord')->field('f.*,a.name,a.thumb')
			->alias('f')
			->join('Admin a', 'a.id = f.check_user_id', 'left')
			->where(['f.action_id'=>$detail['id'],'f.type'=>2])
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
        $expense = (new ExpenseList())->detail($id);
        if ($expense['check_status'] == 2) {
            return to_assign(1, "已审核的报销记录不能删除");
        }
        if ($expense['check_status'] == 5) {
            return to_assign(1, "已打款的报销记录不能删除");
        }
        $data['delete_time'] = time();
        $data['id'] = $id;
        if (Db::name('expense')->update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
}
