<?php
namespace app\project\validate;
use think\Validate;

class ProjectCheck extends Validate
{
    protected $rule = [
        'name'       => 'require',
        'id'         => 'require'
    ];

    protected $message = [
        'name.require'           => '项目名称不能为空',
        'id.require'             => '缺少更新条件',
    ];

    protected $scene = [
        'add'       => ['name'],
        'edit'      => ['id']
    ];
}