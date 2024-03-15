<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\note\controller;

use app\base\BaseController;
use app\note\model\Note as NoteList;
use app\note\validate\NoteCheck;
use app\note\validate\NoteCateCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.title|a.content', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['a.status', '>=', 0];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $note = NoteList::where($where)
                ->field('a.*,c.title as cate_title')
                ->alias('a')
                ->join('NoteCate c', 'a.cate_id = c.id', 'LEFT')
                ->order('a.end_time desc,a.sort desc,a.create_time desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->start_time = empty($item->start_time) ? '-' : date('Y-m-d', $item->start_time);
                    $item->end_time = empty($item->end_time) ? '-' : date('Y-m-d', $item->end_time);
                });
            return table_assign(0, '', $note);
        } else {
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['start_time'] = isset($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $param['end_time'] = isset($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(NoteCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = NoteList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }

                return to_assign();
            } else {
                try {
                    validate(NoteCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['admin_id'] = $this->uid;
                $param['create_time'] = time();
                $sid = NoteList::strict(false)->field(true)->insertGetId($param);
                if ($sid) {
                    add_log('add', $sid, $param);
					$users= Db::name('Admin')->field('id as from_uid')->where(['status' => 1])->column('id');
                    $msg=[
                        'from_uid'=>$this->uid,
                        'title' => $param['title'],
                        'action_id'=>$sid
                    ];
					sendMessage($users,1,$msg);
                }

                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $note = Db::name('Note')->where(['id' => $id])->find();
                View::assign('note', $note);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $note = Db::name('Note')->where(['id' => $id])->find();
        $note['cate_title'] = Db::name('NoteCate')->where(['id' => $note['cate_id']])->value('title');
        $note['admin_name'] = Db::name('Admin')->where(['id' => $note['admin_id']])->value('name');
        View::assign('note', $note);
        return view();
    }

    //删除
    public function delete()
    {
        $id = get_params("id");
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('Note')->update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
	
	//公告类别
    public function note_cate()
    {
        if (request()->isAjax()) {
            $cate = Db::name('NoteCate')->order('create_time asc')->select();
			$list = generateTree($cate);
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //公告类别添加
    public function note_cate_add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(NoteCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $note_array = admin_note_cate_son($param['id']);
                if (in_array($param['pid'], $note_array)) {
                    return to_assign(1, '父级分类不能是该分类本身或其子分类');
                } else {
                    $param['update_time'] = time();
                    $res = Db::name('NoteCate')->strict(false)->field(true)->update($param);
                    if ($res) {
                        add_log('edit', $param['id'], $param);
                    }
                    return to_assign();
                }
            } else {
                try {
                    validate(NoteCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('NoteCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $pid = isset($param['pid']) ? $param['pid'] : 0;
			$cate = $cate = Db::name('NoteCate')->order('id desc')->select()->toArray();
			$cates = set_recursion($cate);
            if ($id > 0) {
                $detail = Db::name('NoteCate')->where(['id' => $id])->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            View::assign('pid', $pid);
            View::assign('cates', $cates);
            return view();
        }
    }

    //公告类别删除
    public function note_cate_delete()
    {
        $id = get_params("id");
        $cate_count = Db::name('NoteCate')->where(["pid" => $id])->count();
        if ($cate_count > 0) {
            return to_assign(1, "该分类下还有子分类，无法删除");
        }
        $content_count = Db::name('Note')->where(["cate_id" => $id,'status'=>1])->count();
        if ($content_count > 0) {
            return to_assign(1, "该分类下还有公告，无法删除");
        }
        if (Db::name('NoteCate')->delete($id) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除分类成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
}
