<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\contract\controller;

use app\base\BaseController;
use app\contract\model\Contract as ContractList;
use app\contract\validate\ContractCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.name|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['cate_id'])) {
                $where[] = ['a.cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['type'])) {
                $where[] = ['a.type', '=', $param['type']];
            }
			if (isset($param['check_status']) && $param['check_status']!='') {
                $where[] = ['a.check_status', '=', $param['check_status']];
            }
            $where[] = ['a.delete_time', '=', 0];
            $where[] = ['a.archive_status', '=', 0];
			
			$uid = $this->uid;
			$auth = isAuth($uid,'contract_admin');
			if($auth==0){
				$whereOr[] =['a.admin_id|a.prepared_uid|a.sign_uid|a.keeper_uid', '=', $uid];
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',a.share_ids)")];
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',a.check_admin_ids)")];
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',a.flow_admin_ids)")];
				$dids = get_department_role($this->uid);
				if(!empty($dids)){
					$whereOr[] =['a.sign_did', 'in', $dids];
				}
			}
			
            $model = new ContractList();
			$list = $model->get_list($param, $where, $whereOr);
            return table_assign(0, '', $list);
        } else {
			$uid = $this->uid;
			$auth = isAuth($uid,'contract_admin');
			View::assign('auth', $auth);
            return view();
        }
    }

    public function archive()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.name|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['cate_id'])) {
                $where[] = ['a.cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['a.cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['type'])) {
                $where[] = ['a.type', '=', $param['type']];
            }
            $where[] = ['a.delete_time', '=', 0];
            $where[] = ['a.archive_status', '=', 1];
			
			$uid = $this->uid;
			$auth = isAuth($uid,'contract_admin');
			if($auth==0){
				$whereOr[] =['a.admin_id|a.prepared_uid|a.sign_uid|a.keeper_uid', '=', $uid];
				$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',a.share_ids)")];
				$dids = get_department_role($this->uid);
				if(!empty($dids)){
					$whereOr[] =['a.sign_did', 'in', $dids];
				}
			}			
            $model = new ContractList();
			$list = $model->get_list($param, $where, $whereOr);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加&&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if (isset($param['sign_time'])) {
                $param['sign_time'] = strtotime($param['sign_time']);
            }
			if (isset($param['start_time'])) {
                $param['start_time'] = strtotime($param['start_time']);
            }
            if (isset($param['end_time'])) {
                $param['end_time'] = strtotime($param['end_time']);
				if ($param['end_time'] <= $param['start_time']) {
					return to_assign(1, "结束时间需要大于开始时间");
				}
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ContractCheck::class)->scene($param['scene'])->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
				$old = Db::name('Contract')->where(['id' => $param['id']])->find();
				$auth = isAuth($this->uid,'contract_admin');
				if($old['check_status'] == 0 || $old['check_status'] == 4){
					if($this->uid!=$old['admin_id'] && $auth==0){
						return to_assign(1, "只有录入人员和合同管理员有权限操作");
					}
					$res = contractList::strict(false)->field(true)->update($param);
					if ($res) {
						add_log('edit', $param['id'], $param);
						to_log($this->uid,$param,$old);
						return to_assign();
					} else {
						return to_assign(1, '操作失败');
					}					
				}
				else{
					return to_assign(1, "当前状态不允许编辑");					
				}
            } else {
                try {
                    validate(ContractCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
				$aid = ContractList::strict(false)->field(true)->insertGetId($param);
				if ($aid) {
					add_log('add', $aid, $param);
					$log_data = array(
						'field' => 'new',
						'action' => 'add',
						'contract_id' => $aid,
						'admin_id' => $param['admin_id'],
						'create_time' => time(),
					);
					Db::name('ContractLog')->strict(false)->field(true)->insert($log_data);
					return to_assign();
				} else {
					return to_assign(1, '操作失败');
				}                
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $type = isset($param['type']) ? $param['type'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
			$is_customer = Db::name('DataAuth')->where('name','contract_admin')->value('expected_1');
			$is_codeno = Db::name('DataAuth')->where('name','contract_admin')->value('expected_2');
			$codeno='';
			if($is_codeno==1){
				$codeno = get_codeno(1);
			}
            View::assign('is_customer', $is_customer);
            View::assign('codeno', $codeno);
            View::assign('id', $id);
            View::assign('type', $type);
            View::assign('pid', $pid);
			View::assign('auth', isAuth($this->uid,'contract_admin'));
            if ($id > 0) {
                $detail = (new ContractList())->detail($id);
				if($detail['check_status'] == 0 || $detail['check_status'] == 4){
					View::assign('detail', $detail);
					return view('edit');
				}
				else{
					echo '<div style="text-align:center;color:red;margin-top:20%;">当前状态不开放编辑，请联系合同管理员</div>';exit;
				}
            }
			if($pid>0){
				$p_contract = Db::name('Contract')->where(['id' => $pid])->find();
                View::assign('p_contract', $p_contract);
			}
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = get_params("id");
        $detail = (new ContractList())->detail($id);
		$auth = isAuth($this->uid,'contract_admin');
		$is_check_admin = 0;
		$is_create_admin = 0;
		$check_record = [];
		if($auth==0){
			$auth_array=[];
			if(!empty($detail['share_ids'])){
				$share_ids = explode(",",$detail['share_ids']);
				$auth_array = array_merge($auth_array,$share_ids);
			}
			if(!empty($detail['check_admin_ids'])){
				$check_admin_ids = explode(",",$detail['check_admin_ids']);
				$auth_array = array_merge($auth_array,$check_admin_ids);
			}
			if(!empty($detail['flow_admin_ids'])){
				$flow_admin_ids = explode(",",$detail['flow_admin_ids']);
				$auth_array = array_merge($auth_array,$flow_admin_ids);
			}		
			array_push($auth_array,$detail['admin_id'],$detail['prepared_uid'],$detail['sign_uid'],$detail['keeper_uid']);
			//部门负责人
			$dids = get_department_role($this->uid);
			if(!in_array($this->uid,$auth_array) && !in_array($detail['sign_did'],$dids)){
				return view('../../base/view/common/roletemplate');
			}
		}
		
		$detail['create_user'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
		
		$detail['copy_user'] = '-';			
		if($detail['copy_uids'] !=''){
			$copy_user = Db::name('Admin')->where('id','in',$detail['copy_uids'])->column('name');
			$detail['copy_user'] = implode(',',$copy_user);
		}
		
		if($detail['check_status']==1){
			$flows = Db::name('FlowStep')->where(['action_id'=>$detail['id'],'type'=>4,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
			$flow_check = get_flow($this->uid,$flows);
			$detail['check_user'] = $flow_check['check_user'];
			$check_user_ids = $flow_check['check_user_ids'];
			if(in_array($this->uid,$check_user_ids)){
				$is_check_admin = 1;
				if($flows['flow_type'] == 4){
					$check_count = Db::name('FlowRecord')->where(['action_id'=>$detail['id'],'type'=>4,'step_id'=>$flows['id'],'check_user_id'=>$this->uid])->count();
					if($check_count>0){
						$is_check_admin = 0;
					}
				}
			}
			
		}
		else{
			//获取合同审批流程
			$flows = get_type_department_flows(8,$this->did);
			$detail['check_user'] = '-';
		}

		if($detail['admin_id'] == $this->uid){
			$is_create_admin = 1;
		}
		
		$file_array_other = Db::name('ContractFile')
			->field('cf.id,f.filepath,f.name,f.filesize,f.fileext,f.create_time,f.admin_id')
			->alias('cf')
			->join('File f', 'f.id = cf.file_id', 'LEFT')
			->order('cf.create_time asc')
			->where(array('cf.contract_id' => $id, 'cf.delete_time' => 0))
			->select()->toArray();
		$detail['file_array_other'] = $file_array_other;
		
		$check_record = Db::name('FlowRecord')->field('f.*,a.name,a.thumb')
			->alias('f')
			->join('Admin a', 'a.id = f.check_user_id', 'left')
			->where(['f.action_id'=>$detail['id'],'f.type'=>4])
			->order('check_time asc')
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
		
		View::assign('is_check_admin', $is_check_admin);
		View::assign('is_create_admin', $is_create_admin);
		View::assign('check_record', $check_record);
		View::assign('flows', $flows);
        View::assign('auth', $auth);
        View::assign('detail', $detail);
		return view();
    }
    //删除
    public function delete()
    {
		if (request()->isDelete()) {
			$id = get_params("id");
			$data['id'] = $id;
			$data['delete_time'] = time();
			if (Db::name('Contract')->update($data) !== false) {
				add_log('delete', $id);
				$log_data = array(
					'field' => 'del',
					'action' => 'delete',
					'contract_id' => $id,
					'admin_id' => $this->uid,
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
}
