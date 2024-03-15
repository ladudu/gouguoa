<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\contract\validate;

use think\Validate;

class ContractCheck extends Validate
{
    protected $rule = [
        'name' => 'require',
        'code' => 'require',
        'id'   => 'require',
        'cost'   => 'number',
        'cate_id' => 'require',
    ];

    protected $message = [
        'name.require' => '合同名称不能为空',
		'code.require' => '合同编号不能为空',
        'cost.number' => '价格只能是数字',
        'cate_id.require' => '所属分类为必选',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['name', 'cate_id', 'code'],
        'edit' => ['name', 'cate_id', 'code', 'id'],
        'change' => ['id'],
    ];
}
