<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\project\controller;

use app\base\BaseController;
use app\project\model\ProjectDocument as DocumentList;
use app\project\validate\DocumentCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Document extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $param['uid'] = $this->uid;
            $list = (new DocumentList())->list($param);
            return table_assign(0, '', $list);
        } else {
			View::assign('project', get_project($this->uid));
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
			if (isset($param['file'])) {
				unset($param['file']);
			}
            if (isset($param['end_time'])) {
                $param['end_time'] = strtotime(urldecode($param['end_time']));
            }if (isset($param['flow_status'])) {
                if ($param['flow_status'] == 3) {
                    $param['over_time'] = time();
                } else {
                    $param['over_time'] = 0;
                }
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $detail = (new DocumentList())->detail($param['id']);
                try {
                    validate(DocumentCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = DocumentList::where('id', $param['id'])->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
					add_project_log($this->uid,'document',$param, $detail);
                }
                return to_assign();
            } else {
                try {
                    validate(DocumentCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $sid = DocumentList::strict(false)->field(true)->insertGetId($param);
                if ($sid) {
                    add_log('add', $sid, $param);
                    $log_data = array(
                        'module' => 'document',
                        'document_id' => $sid,
                        'new_content' => $param['title'],
                        'field' => 'new',
                        'action' => 'add',
                        'admin_id' => $this->uid,
                        'create_time' => time(),
                    );
                    Db::name('ProjectLog')->strict(false)->field(true)->insert($log_data);
                }
                return to_assign();
            }
        } else {
			$id = isset($param['id']) ? $param['id'] : 0;
			$project_id = isset($param['project_id']) ? $param['project_id'] : 0;
			if($id>0){
				$detail = (new DocumentList())->detail($param['id']);
				if($detail['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
					$detail['fileArray'] = $fileArray;
				}
				View::assign('detail', $detail);
			}
            View::assign('project_id', $project_id);
            View::assign('id', $id);
            return view();
        }
    }

    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = (new DocumentList())->detail($id);
        if (empty($detail)) {
			echo '<div style="text-align:center;color:red;margin-top:20%;">该文档不存在</div>';exit;
        } else {
			if($detail['file_ids'] !=''){
				$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['fileArray'] = $fileArray;
			}
            $project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
            if (in_array($detail['project_id'], $project_ids) || ($this->uid = $detail['admin_id'])) {
                View::assign('detail', $detail);
                View::assign('id', $id);
                return view();
            }
            else{
				echo '<div style="text-align:center;color:red;margin-top:20%;">您没权限查看该文档</div>';exit;
            }
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $detail = Db::name('ProjectDocument')->where('id', $id)->find();
            if ($detail['admin_id'] != $this->uid) {
                return to_assign(1, "你不是该文档的创建人，无权限删除");
            }
            if (Db::name('ProjectDocument')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                $log_data = array(
                    'module' => 'document',
                    'field' => 'delete',
                    'action' => 'delete',
                    'document_id' => $detail['id'],
                    'admin_id' => $this->uid,
                    'old_content' => '',
                    'new_content' => $detail['title'],
                    'create_time' => time(),
                );
                Db::name('ProjectLog')->strict(false)->field(true)->insert($log_data);
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
}
