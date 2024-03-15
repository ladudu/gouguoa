<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\oa\controller;

use app\api\BaseController;
use app\oa\model\WorkComment;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	//获取评论列表
    public function work_comment()
    {
		$param = get_params();
		$list = new WorkComment();
		$content = $list->get_list($param);
		return to_assign(0, '', $content);
    }
	
    //添加修改评论内容
    public function add_comment()
    {
		$param = get_params();	
		if (!empty($param['id']) && $param['id'] > 0) {
			$param['update_time'] = time();
			unset($param['pid']);
			unset($param['padmin_id']);
            $res = WorkComment::where(['admin_id' => $this->uid,'id'=>$param['id']])->strict(false)->field(true)->update($param);
			if ($res) {
				add_log('edit', $param['id'], $param,'评论');
				return to_assign();
			}
        } else {
            $param['create_time'] = time();
            $param['admin_id'] = $this->uid;
            $cid = WorkComment::strict(false)->field(true)->insertGetId($param);
			if ($cid) {
				add_log('add', $cid, $param,'评论');
				return to_assign();
			}			
		}
    }
	
	//删除评论内容
    public function delete_comment()
    {
		if (request()->isDelete()) {
			$id = get_params("id");
			$res = WorkComment::where('id',$id)->strict(false)->field(true)->update(['delete_time'=>time()]);
			if ($res) {
				add_log('delete', $id,[],'评论');
				return to_assign(0, "删除成功");
			} else {
				return to_assign(1, "删除失败");
			}
		}else{
			return to_assign(1, "错误的请求");
		}
    }
}
