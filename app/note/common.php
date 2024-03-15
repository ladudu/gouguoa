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
//读取公告分类子分类ids
function admin_note_cate_son($id = 0, $is_self = 1)
{
    $note = Db::name('NoteCate')->order('create_time asc')->select();
    $note_list = get_data_node($note, $id);
    $note_array = array_column($note_list, 'id');
    if ($is_self == 1) {
        //包括自己在内
        $note_array[] = $id;
    }
    return $note_array;
}

//读取公告分类列表
function note_cate()
{
    $cate = Db::name('NoteCate')->order('id desc')->select()->toArray();
    return $cate;
}