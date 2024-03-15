<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\customer\validate;

use think\Validate;

class CustomerContactCheck extends Validate
{
    protected $rule = [
        'name' => 'require',
        'mobile' => 'require',
        'id' => 'require',
    ];

    protected $message = [
        'name.require' => '联系人姓名不能为空',
        'mobile' => '手机号码不能为空',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['name','mobile'],
        'edit' => ['id', 'name','mobile'],
    ];
}
