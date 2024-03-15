<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\article\validate;

use think\Validate;

class ArticleCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:article',
        'content' => 'require',
        'id' => 'require',
        'cate_id' => 'require',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'title.unique' => '同样的文章标题已经存在',
        'cate_id.require' => '所属分类为必选',
        'id.require' => '缺少更新条件',
    ];

    protected $scene = [
        'add' => ['title', 'cate_id', 'content'],
        'edit' => ['title', 'cate_id', 'content', 'id'],
    ];
}
