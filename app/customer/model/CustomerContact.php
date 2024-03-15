<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\customer\model;

use think\facade\Db;
use think\Model;

class CustomerContact extends Model
{
	// 获取详情
    public function detail($id)
    {
        $detail = Db::name('CustomerContact')->where(['id' => $id])->find();
        if (!empty($detail)) {
            $detail['create_time'] = date('Y-m-d H:i:s', $detail['create_time']);
			$detail['customer'] = Db::name('Customer')->where(['id' => $detail['cid']])->value('name');
        }
        return $detail;
    }
}
