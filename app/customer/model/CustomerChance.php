<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\customer\model;

use think\facade\Db;
use think\Model;

class CustomerChance extends Model
{
	const ZERO = 0;
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const FORE = 4;
    const FIVE = 5;
    const SIX = 6;
    const SEVEN = 7;

	public static $Stage = [
        self::ZERO => '未设置',
        self::ONE => '立项评估',
        self::TWO => '初期沟通',
        self::THREE => '需求分析',
        self::FORE => '商务谈判',
        self::FIVE => '方案制定',
        self::SIX => '合同签订',
        self::SEVEN => '失单',
    ];
	
    // 获取详情
    public function detail($id)
    {
       $detail = Db::name('CustomerChance')->where(['id' => $id])->find();
        if (!empty($detail)) {				
			$detail['customer'] = Db::name('Customer')->where(['id' => $detail['cid']])->value('name');	
            $detail['create_time'] = date('Y-m-d', $detail['create_time']);
            $detail['expected_time'] = date('Y-m-d', $detail['expected_time']);
            $detail['discovery_time'] = date('Y-m-d', $detail['discovery_time']);
			$detail['belong_name'] = Db::name('Admin')->where(['id' => $detail['belong_uid']])->value('name');			
			$assist_names = Db::name('Admin')->where([['id','in',$detail['assist_ids']]])->column('name');
			$detail['assist_names'] = implode(',',$assist_names);
			$detail['stage_name'] = self::$Stage[(int) $detail['stage']];
        }
        return $detail;
    }
}
