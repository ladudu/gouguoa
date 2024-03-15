<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\customer\model;

use think\facade\Db;
use think\Model;

class Customer extends Model
{
	protected $autoWriteTimestamp=false;
	const ZERO = 0;
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const FORE = 4;
    const FIVE = 5;
    const SIX = 6;
    const SEVEN = 7;
	
	public static $Status = [
        self::ZERO => '未设置',
        self::ONE => '新进客户',
        self::TWO => '跟进客户',
        self::THREE => '正式客户',
        self::FORE => '流失客户',
		self::FIVE => '已成交客户',
    ];
	public static $IntentStatus = [
        self::ZERO => '未设置',
        self::ONE => '意向不明',
        self::TWO => '意向模糊',
        self::THREE => '意向一般',
        self::FORE => '意向强烈',
        self::FIVE => '已成交',
    ];
	
	public static $Type = [
		self::ZERO => '其他',
        self::ONE => '电话',
        self::TWO => '微信',
        self::THREE => 'QQ',
        self::FORE => '上门'
	
	];
	public static $Stage = [
		self::ZERO => '未设置',
        self::ONE => '立项评估',
        self::TWO => '初期沟通',
        self::THREE => '需求分析',
        self::FORE => '方案制定',
        self::FIVE => '商务谈判',
        self::SIX => '合同签订',
        self::SEVEN => '失单'
	];
	
    // 获取详情
    public function detail($id)
    {
        $detail = Db::name('Customer')->where(['id' => $id])->find();
        if (!empty($detail)) {
			$file_array = Db::name('CustomerFile')
				->field('cf.id,f.filepath,f.name,f.filesize,f.fileext,f.create_time,f.admin_id')
				->alias('cf')
				->join('File f', 'f.id = cf.file_id', 'LEFT')
				->order('cf.create_time asc')
				->where(array('cf.customer_id' => $id, 'cf.delete_time' => 0))
				->select()->toArray();
				
			$trace_array = Db::name('CustomerTrace')->where(array('cid' => $id, 'delete_time' => 0))->order('follow_time desc')->limit(1)->select()->toArray();
				
			$detail['status_name'] = self::$Status[(int) $detail['status']];
            $detail['create_time'] = date('Y-m-d', $detail['create_time']);
			$detail['belong_department'] = Db::name('Department')->where(['id' => $detail['belong_did']])->value('title');
			$detail['belong_name'] = Db::name('Admin')->where(['id' => $detail['belong_uid']])->value('name');
			$detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
			
			$share_names = Db::name('Admin')->where([['id','in',$detail['share_ids']]])->column('name');
			$detail['share_names'] = implode(',',$share_names);
			
			$detail['file_array'] = $file_array;
			$detail['trace'] = [];
			if(!empty($trace_array)){
				$trace = $trace_array[0];
				$trace['follow_time'] = date('Y-m-d H:i', $trace['follow_time']);
				$trace['next_time'] = date('Y-m-d H:i', $trace['next_time']);
				$trace['contact_name'] = Db::name('CustomerContact')->where('id',$trace['contact_id'])->value('name');
				$trace['stage_name'] = self::$Stage[(int) $trace['stage']];
				$trace['type_name'] = self::$Type[(int) $trace['type']];
				$detail['trace'] = $trace;
			}
        }
        return $detail;
    }
}
