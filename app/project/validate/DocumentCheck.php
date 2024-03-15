<?php
namespace app\project\validate;

use think\Validate;

class DocumentCheck extends Validate
{
    protected $rule = [
        'title' => 'require',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '文档标题不能为空',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title'],
        'edit' => ['id'],
    ];
}
