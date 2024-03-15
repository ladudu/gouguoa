<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\finance\controller;

use app\api\BaseController;
use app\finance\model\Expense;
use app\finance\model\Invoice;
use app\finance\model\InvoiceIncome;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
    //删除报销附件
    public function del_expense_interfix()
    {
        $id = get_params("id");
        $admin_id = Db::name('ExpenseInterfix')->where('id', $id)->value('admin_id');
        if ($admin_id == $this->uid) {
            if (Db::name('ExpenseInterfix')->where('id', $id)->delete() !== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(1, "删除失败");
            }
        } else {
            return to_assign(1, "您不是上传者，没权限删除该报销数据");
        }
    }
	
	//报销设置为已打款
    public function topay()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthExpense($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有打款权限，请联系管理员或者HR");
			}
			//打款，数据操作
            $param['check_status'] = 5;
            $param['pay_admin_id'] = $this->uid;
            $param['pay_time'] = time();
            $res = Expense::where('id', $param['id'])->strict(false)->field(true)->update($param);
            if ($res !== false) {
				add_log('topay', $param['id'],$param,'报销');
				//发送消息通知
				$detail = Expense::where(['id' => $param['id']])->find();
				$msg=[
					'create_time'=>$detail['create_time'],
					'title'=>'报销',
					'action_id'=>$detail['id']
				];
				$users = $detail['admin_id'];
				sendMessage($users,34,$msg);
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
    //开具发票
    public function open()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有开票权限，请联系管理员或者HR");
			}
			$status = Invoice::where(['id' => $param['id']])->value('check_status');
            if ($status == 2) {
                $param['check_status'] = 5;
                $param['open_admin_id'] = $this->uid;
            }
			if(isset($param['open_time'])){
				$param['open_time'] = strtotime(urldecode($param['open_time']));
			}
            $res = Invoice::where('id', $param['id'])->strict(false)->field('code,check_status,open_time,open_admin_id,delivery,other_file_ids')->update($param);
            if ($res !== false) {
				add_log('open', $param['id'],$param,'发票');
                return to_assign();
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }
	
    //作废发票
    public function tovoid()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有作废发票权限，请联系管理员或者HR");
			}
            if ($param['check_status'] == 10) {
                $count = InvoiceIncome::where(['inid'=>$param['id'],'status'=>1])->count();
                if($count>0){
                    return to_assign(1, "发票已经新增有到账记录，请先反到账后再作废发票");
                }
                else{
                    $param['update_time'] = time();
                }
            }
            $res = Invoice::where('id', $param['id'])->strict(false)->field('check_status')->update($param);
            if ($res !== false) {
                return to_assign();
                add_log('tovoid', $param['id'],$param,'发票');
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }

    //反作废发票
    public function novoid()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$auth = isAuthInvoice($this->uid);
			if($auth == 0){
				return to_assign(1, "你没有作废发票权限，请联系管理员或者HR");
			}
			$param['check_status'] = 5;
			$param['update_time'] = time();
			add_log('tovoid', $param['id'],$param,'发票');
            $res = Invoice::where('id', $param['id'])->strict(false)->field('check_status')->update($param);
            if ($res !== false) {
                return to_assign();
				add_log('novoid', $param['id'],$param,'发票');
            } else {
                return to_assign(1, "操作失败");
            }
        }
    }

}
