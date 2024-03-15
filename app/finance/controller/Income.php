<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\finance\controller;

use app\base\BaseController;
use app\finance\model\Invoice;
use app\finance\model\InvoiceIncome;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Income extends BaseController
{
    public function index()
    {
		$auth = isAuthIncome($this->uid);
        if (request()->isAjax()) {
            $param = get_params();
            $where = [];
            $where[] = ['delete_time', '=', 0];
            $where[] = ['check_status', '=', 5];
			//按时间检索
			if (!empty($param['diff_time'])) {
				$diff_time =explode('~', $param['diff_time']);
				$where[] = ['enter_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
			}
            if (isset($param['is_cash']) && $param['is_cash']!='') {
                $where[] = ['is_cash', '=', $param['is_cash']];
            }
			if($auth == 0){
				$where[] = ['admin_id','=',$this->uid];
			}
			$model = new Invoice();
            $invoice = $model->income_list($param, $where);
            return table_assign(0, '', $invoice);
        } else {
			View::assign('auth', $auth);
            return view();
        }
    }

    //查看
    public function add()
    {
        $param = get_params();
		$auth = isAuthIncome($this->uid);
        if (request()->isAjax()) {   
			if($auth == 0){
				return to_assign(1, "你没有到账管理权限，请联系管理员或者HR");
			}
            $inid = $param['inid'];   
            $admin_id = $this->uid;
            //计算已到账的金额
            $hasIncome = InvoiceIncome::where(['inid'=>$inid,'status'=>1])->sum('amount');
            //查询发票金额
            $invoiceAmount = Invoice::where(['id'=>$inid])->value('amount');
            if($param['enter_type']==1){ //单个到账记录
                //相关内容多个数组
                $enterPriceData=isset($param['amount'])? $param['amount'] : '';
                $enterTimeData=isset($param['enter_time'])? $param['enter_time'] : '';
                $remarksData=isset($param['remarks'])? $param['remarks'] : '';

                //把合同协议关联的单个内容的发票入账明细重新添加
                if($enterPriceData){
                    $enter_price = 0;
                    $insert = [];
		            $time = time();
                    foreach ($enterPriceData as $key => $value) {
                        if (!$value ) continue;
                        $insert[] = [
                            'inid' => $inid,
						    'amount' 	=> $value,
						    'enter_time' => $enterTimeData[$key]? strtotime($enterTimeData[$key]) : 0,
						    'remarks' 	    => $remarksData[$key],
						    'admin_id' 	    => $admin_id,
						    'create_time'		=> $time
						];
                        $enter_price += $value*100;
                    }
                    if(($enter_price + $hasIncome*100)> $invoiceAmount*100){
                        return to_assign(1,'到账金额大于发票金额，不允许保存');
                    }
                    else{
                        $res = InvoiceIncome::strict(false)->field(true)->insertAll($insert);
                        if($res!==false){
                            if(($enter_price + $hasIncome*100) == $invoiceAmount*100){
                                //发票全部到账
                                Invoice::where(['id'=>$inid])->update(['is_cash'=>2,'enter_amount'=>$invoiceAmount,'enter_time'=>time()]);
                            }
                            else if(($enter_price + $hasIncome*100) < $invoiceAmount*100){
                                $incomeTotal=($enter_price + $hasIncome*100)/100;
                                //发票部分到账
                                Invoice::where(['id'=>$inid])->update(['is_cash'=>1,'enter_amount'=>$incomeTotal,'enter_time'=>time()]);
                            }
                            add_log('add',$inid,$param);
                            return to_assign();
                        }
                        else{
                            return to_assign(1,'保存失败');
                        }
                    }
                }
                else{
                    return to_assign(1,'提交的到账数据异常，请核对再提交');
                }         
            }
            else if($param['enter_type']==2){ //全部到账记录
                $enter_price = ($invoiceAmount*100-$hasIncome*100)/100;
                $data = [
                    'inid' => $inid,
                    'amount' => $enter_price,
                    'enter_time' => isset($param['enter_time'])? strtotime($param['enter_time']) : 0,
                    'remarks' => '一次性全部到账',
                    'admin_id' => $admin_id,
                    'create_time' => time()
                ];
                $res = InvoiceIncome::strict(false)->field(true)->insertGetId($data);
                if($res!==false){
                    //设置发票全部到账
                    Invoice::where(['id'=>$inid])->update(['is_cash'=>2,'enter_amount'=>$invoiceAmount,'enter_time'=>time()]);
                    add_log('add',$inid,$param);
                    return to_assign();
                }
            }
            else if ($param['enter_type']==3) {//全部反账记录
                //作废初始化发票到账数据
                $res = InvoiceIncome::where(['inid'=>$inid])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    //设置发票全部没到账
                    Invoice::where(['id'=>$inid])->update(['is_cash'=>0,'enter_amount'=>0,'enter_time'=>0]);
                    add_log('tovoid',$inid,$param);
                    return to_assign();
                }                
            }
        }
        else{
			if($auth == 0){
				return view('../../base/view/common/roletemplate');
			}
            $id = isset($param['id']) ? $param['id']: 0 ;
			$model = new Invoice();
            $detail = $model->detail($id);
			if(empty($detail)){
				throw new \think\exception\HttpException(406, '找不到记录');
			}
			if($detail['file_ids'] !=''){
				$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['fileArray'] = $fileArray;
			}
			
			if($detail['other_file_ids'] !=''){
				$fileArrayOther = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
				$detail['fileArrayOther'] = $fileArrayOther;
			}
			$detail['not_income'] =  ($detail['amount']*100 - $detail['enter_amount']*100)/100;
			//已到账的记录
			$detail['income'] = InvoiceIncome::field('i.*,a.name as admin')
				->alias('i')
				->join('Admin a', 'a.id = i.admin_id', 'LEFT')
				->where(['i.inid'=>$id,'i.status'=>1])
				->order('i.enter_time desc')
				->select();
            View::assign('uid', $this->uid);
            View::assign('id', $id);
            View::assign('detail', $detail);
            return view();
        }
    }
    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $model = new Invoice();
        $detail = $model->detail($id);
		if(empty($detail)){
			throw new \think\exception\HttpException(406, '找不到记录');
		}
		$detail['not_income'] =  ($detail['amount']*100 - $detail['enter_amount']*100)/100;
		//已到账的记录
		$detail['income'] = InvoiceIncome::field('i.*,a.name as admin')
			->alias('i')
			->join('Admin a', 'a.id = i.admin_id', 'LEFT')
			->where(['i.inid'=>$id,'i.status'=>1])
			->order('i.enter_time desc')
			->select();
		if($detail['file_ids'] !=''){
			$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['fileArray'] = $fileArray;
		}
		
		if($detail['other_file_ids'] !=''){
			$fileArrayOther = Db::name('File')->where('id','in',$detail['other_file_ids'])->select();
			$detail['fileArrayOther'] = $fileArrayOther;
		}
        View::assign('uid', $this->uid);
        View::assign('detail', $detail);
        return view();
    }

    //删除到账记录
    public function delete()
    {
        $param = get_params();
        if (request()->isAjax()) {
            //作废初始化发票到账数据
            $income =InvoiceIncome::where(['id'=>$param['id']])->find();
            $invoice = Invoice::where(['id'=>$income['inid']])->find();
            if($income){
                $res = InvoiceIncome::where(['id'=>$param['id']])->update(['status'=>'6','update_time'=>time()]);
                if($res!==false){
                    if($income['amount']*100 == $invoice['amount']*100){
                        //发票全部反到账
                        Invoice::where(['id'=>$income['inid']])->update(['is_cash'=>0,'enter_amount'=>0,'enter_time'=>0]);
                    }
                    else if($income['amount']*100 < $invoice['amount']*100){
                        $incomeTotal=InvoiceIncome::where(['inid'=>$income['inid'],'status'=>1])->sum('amount');
                        //发票部分到账
                        Invoice::where(['id'=>$income['inid']])->update(['is_cash'=>1,'enter_amount'=>$incomeTotal,'enter_time'=>time()]);
                    }
                    add_log('enter',$income['inid'],$invoice);
                    return to_assign();
                }
                else{
                    return to_assign(1,'操作失败');
                }
            }
        }
    }
}
