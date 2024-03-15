<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
/**
======================
 *模块数据获取公共文件
======================
 */
use think\facade\Db;

//读取知识分类子分类ids
function admin_article_cate_son($id = 0, $is_self = 1)
{
    $article = Db::name('ArticleCate')->order('id desc')->select()->toArray();
    $article_list = get_data_node($article, $id);
    $article_array = array_column($article_list, 'id');
    if ($is_self == 1) {
        //包括自己在内
        $article_array[] = $id;
    }
    return $article_array;
}
//读取知识分类列表
function article_cate()
{
    $cate = Db::name('ArticleCate')->order('id desc')->select()->toArray();
    return $cate;
}