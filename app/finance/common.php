<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
/**
======================
 *模块数据获取公共文件
======================
 */
use think\facade\Db;

//是否是报销打款管理员,count>1即有权限
function isAuthExpense($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'finance_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}

//是否是发票管理员,count>1即有权限
function isAuthInvoice($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'finance_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',conf_1)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}

//是否是到账管理员,count>1即有权限
function isAuthIncome($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'finance_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',conf_2)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}

//读取开票主体
function finance_invoice_subject()
{
    $subject = Db::name('InvoiceSubject')->where(['status' => 1])->order('id desc')->select()->toArray();
    return $subject;
}

//读取报销类型
function finance_expense_cate()
{
    $cate = Db::name('ExpenseCate')->where(['status' => 1])->order('id desc')->select()->toArray();
    return $cate;
}