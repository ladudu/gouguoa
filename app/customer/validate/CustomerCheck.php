<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\customer\validate;

use think\Validate;

class CustomerCheck extends Validate
{
    protected $rule = [
        'name' => 'require|unique:customer',
        'id'   => 'require',
    ];

    protected $message = [
        'name.require' => '客户名称不能为空',
        'name.unique' => '同样的客户名称已经存在',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['name',],
        'edit' => ['name','id'],
        'change' => ['id'],
    ];
}
