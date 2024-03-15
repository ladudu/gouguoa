<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class dataauth extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
			$list = Db::name('DataAuth')->select();
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //编辑配置信息
    public function edit()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['update_time'] = time();
            $res = Db::name('DataAuth')->strict(false)->field(true)->update($param);
            if ($res) {
                add_log('edit', $param['id'], $param);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $detail = $this->auth_detail($id);
            $module = strtolower(app('http')->getName());
            $class = strtolower(app('request')->controller());
            $action = strtolower(app('request')->action());
            $template = $module . '/view/' . $class . '/' . $detail['name'] . '.html';
            View::assign('detail', $detail);
            if (isTemplate($template)) {
                return view($detail['name']);
            } else {
                return view('../../base/view/common/errortemplate', ['file' => $template]);
            }
        }
    }
	
	public function auth_detail($id)
    {
        $detail = Db::name('DataAuth')->where('id',$id)->find();
		if($detail['name'] =='user_admin'){
			$uids = Db::name('Admin')->where('id', 'in', $detail['uids'])->column('name');
			$detail['unames'] = implode(',', $uids);
			
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
			$detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		if($detail['name'] =='oa_admin'){
			$uids = Db::name('Admin')->where('id', 'in', $detail['uids'])->column('name');
			$detail['unames'] = implode(',', $uids);
			
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
			$detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		if($detail['name'] =='finance_admin'){
			$unames = Db::name('Admin')->where('id', 'in', $detail['uids'])->column('name');
            $detail['unames'] = implode(',', $unames);
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
			$conf_2_str = Db::name('Admin')->where('id', 'in', $detail['conf_2'])->column('name');
            $detail['conf_2_str'] = implode(',', $conf_2_str);
			$conf_3_str = Db::name('Admin')->where('id', 'in', $detail['conf_3'])->column('name');
            $detail['conf_3_str'] = implode(',', $conf_3_str);
			$conf_4_str = Db::name('Admin')->where('id', 'in', $detail['conf_4'])->column('name');
            $detail['conf_4_str'] = implode(',', $conf_4_str);
			$conf_5_str = Db::name('Admin')->where('id', 'in', $detail['conf_5'])->column('name');
            $detail['conf_5_str'] = implode(',', $conf_5_str);
		}
		if($detail['name'] =='customer_admin'){
			$uids = Db::name('Admin')->where('id', 'in', $detail['uids'])->column('name');
			$detail['unames'] = implode(',', $uids);
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
			$conf_2_str = Db::name('Admin')->where('id', 'in', $detail['conf_2'])->column('name');
            $detail['conf_2_str'] = implode(',', $conf_2_str);
			$conf_3_str = Db::name('Admin')->where('id', 'in', $detail['conf_3'])->column('name');
            $detail['conf_3_str'] = implode(',', $conf_3_str);
		}
		if($detail['name'] =='contract_admin'){
			$uids = Db::name('Admin')->where('id', 'in', $detail['uids'])->column('name');
			$detail['unames'] = implode(',', $uids);
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
			$conf_2_str = Db::name('Admin')->where('id', 'in', $detail['conf_2'])->column('name');
            $detail['conf_2_str'] = implode(',', $conf_2_str);
		}
		if($detail['name'] =='project_admin'){
			$uids = Db::name('Admin')->where('id', 'in', $detail['uids'])->column('name');
			$detail['unames'] = implode(',', $uids);
		}
		return $detail;
    }
}
