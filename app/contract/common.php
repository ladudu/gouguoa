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

//是否是合同管理员权限
function contract_auth($uid)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', 'contract_admin'];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}
//读取分类列表
function contract_cate()
{
    $cate = Db::name('ContractCate')->where(['status' => 1])->order('id desc')->select()->toArray();
    return $cate;
}

//读取签约主体
function contract_subject()
{
    $subject = Db::name('InvoiceSubject')->where(['status' => 1])->order('id desc')->select()->toArray();
    return $subject;
}

//写入日志
function to_log($uid,$new,$old)
{
	$log_data = [];
	$key_array = ['id', 'create_time', 'update_time', 'sign_did'];
	foreach ($new as $key => $value) {
		if (!in_array($key, $key_array)) {
			if(isset($old[$key]) && ($old[$key]!=$value)){
				$log_data[] = array(
					'field' => $key,
					'contract_id' => $new['id'],
					'admin_id' => $uid,
					'old_content' => $old[$key],
					'new_content' => $value,
					'create_time' => time(),
				);
			}
		}
	}
	Db::name('ContractLog')->strict(false)->field(true)->insertAll($log_data);
}