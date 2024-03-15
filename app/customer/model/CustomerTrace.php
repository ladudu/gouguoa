<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\customer\model;

use think\facade\Db;
use think\Model;

class CustomerTrace extends Model
{
	const ZERO = 0;
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const FORE = 4;
    const FIVE = 5;
    const SIX = 6;
    const SEVEN = 7;
	
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
        self::FORE => '商务谈判',
        self::FIVE => '方案制定',
        self::SIX => '合同签订',
        self::SEVEN => '失单',
    ];
	
    // 获取详情
    public function detail($id)
    {
        $detail = Db::name('CustomerTrace')->where(['id' => $id])->find();
        if (!empty($detail)) {
			$detail['chance_name'] ='-';
			if($detail['chance_id'] >0){
				$detail['chance_name'] =Db::name('CustomerChance')->where(['id' => $detail['chance_id']])->value('title');
			}
			$detail['contact_name'] =Db::name('CustomerContact')->where(['id' => $detail['contact_id']])->value('name');
            $detail['stage_name'] = self::$Stage[(int) $detail['stage']];
            $detail['type_name'] = self::$Type[(int) $detail['type']];
            $detail['create_time'] = date('Y-m-d H:i:s', $detail['create_time']);
            $detail['follow_time'] = date('Y-m-d H:i:s', $detail['follow_time']);
            $detail['next_time'] = date('Y-m-d H:i:s', $detail['next_time']);
			$detail['customer'] = Db::name('Customer')->where(['id' => $detail['cid']])->value('name');
        }
        return $detail;
    }
}
