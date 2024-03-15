<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use app\home\model\AdminLog;
use app\user\validate\AdminCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $admin_id = $this->uid;
            //未读消息统计
            $msg_map[] = ['to_uid', '=', $admin_id];
            $msg_map[] = ['read_time', '=', 0];
            $msg_map[] = ['status', '=', 1];
            $msg_count = Db::name('Message')->where($msg_map)->count();
            $statistics['msg_num'] = $msg_count;
            return to_assign(0, 'ok', $statistics);
        } else {
            $admin = Db::name('Admin')->where('id',$this->uid)->find();
            if (get_cache('menu' . $this->uid)) {
                $list = get_cache('menu' . $this->uid);
            } else {
                $adminGroup = Db::name('PositionGroup')->where(['pid' => $admin['position_id']])->column('group_id');
                $adminMenu = Db::name('AdminGroup')->where('id', 'in', $adminGroup)->column('rules');
                $adminMenus = [];
                foreach ($adminMenu as $k => $v) {
                    $v = explode(',', $v);
                    $adminMenus = array_merge($adminMenus, $v);
                }
                $menu = Db::name('AdminRule')->where(['menu' => 1, 'status' => 1])->where('id', 'in', $adminMenus)->order('sort asc,id asc')->select()->toArray();
                $list = list_to_tree($menu);
                \think\facade\Cache::tag('adminMenu')->set('menu' . $this->uid, $list);
            }
            View::assign('menu', $list);
			View::assign('admin',$admin);
			View::assign('web',get_system_config('web'));			
            return View();
        }
    }

    public function main()
    {
        $install = false;
        if (file_exists(CMS_ROOT . 'app/install')) {
            $install = true;
        }
        $total = [];
        $adminCount = Db::name('Admin')->where('status', '1')->count();
        $approveCount = Db::name('Approve')->count();
        $noteCount = Db::name('Note')->where('status', '1')->count();
        $expenseCount = Db::name('Expense')->where('delete_time', '0')->count();
        $invoiceCount = Db::name('Invoice')->where('delete_time', '0')->count();
        $total[] = array(
            'name' => '员工',
            'num' => $adminCount,
        );
		$total[] = array(
            'name' => '公告',
            'num' => $noteCount,
        );
        $total[] = array(
            'name' => '审批',
            'num' => $approveCount,
        );
        $total[] = array(
            'name' => '报销',
            'num' => $expenseCount,
        );
        $total[] = array(
            'name' => '发票',
            'num' => $invoiceCount,
        );
		
		$handle=[
			'approve'=>Db::name('Approve')->where([['', 'exp', Db::raw("FIND_IN_SET('{$this->uid}',check_admin_ids)")]])->count(),
			'expenses'=>Db::name('Expense')->where([['', 'exp', Db::raw("FIND_IN_SET('{$this->uid}',check_admin_ids)")],['delete_time', '=', 0]])->count(),
			'invoice'=>Db::name('Invoice')->where([['', 'exp', Db::raw("FIND_IN_SET('{$this->uid}',check_admin_ids)")],['delete_time', '=', 0]])->count(),
			'income'=>Db::name('Invoice')->where([['is_cash', '<', 2],['admin_id','=',$this->uid],['check_status', '=', 5],['delete_time', '=', 0]])->count(),
			'contract'=>0,
			'task'=>0
		];
		
        $module = Db::name('AdminModule')->column('name');
        if (in_array('customer', $module)) {
			
			$whereCustomer = array();
			$whereCustomerOr = array();
			$uid = $this->uid;
			$dids = get_department_role($uid);
			
			$whereCustomer[] = ['delete_time', '=', 0];
			$whereCustomerOr[] =['belong_uid', '=', $uid];	
			if(!empty($dids)){
				$whereCustomerOr[] =['belong_did', 'in', $dids];
			}			
			$whereCustomerOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			
            $customerCount = Db::name('Customer')->where($whereCustomer)
			->where(function ($query) use($whereCustomerOr) {
					$query->whereOr($whereCustomerOr);
				})
			->count();
            $total[] = array(
                'name' => '客户',
                'num' => $customerCount,
            );
        }
        if (in_array('contract', $module)) {
			$whereContract = array();
			$whereContractOr = array();
			$uid = $this->uid;
			
			$whereContract[] = ['delete_time', '=', 0];
			$whereContractOr[] =['admin_id|prepared_uid|sign_uid|keeper_uid', '=', $uid];
			$whereContractOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',share_ids)")];
			$whereContractOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',check_admin_ids)")];
			$whereContractOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',flow_admin_ids)")];
			$dids = get_department_role($uid);
			if(!empty($dids)){
				$whereContractOr[] =['sign_did', 'in', $dids];
			}
			
            $contractCount = Db::name('Contract')->where($whereContract)
			->where(function ($query) use($whereContractOr) {
					$query->whereOr($whereContractOr);
				})
			->count();
            $total[] = array(
                'name' => '合同',
                'num' => $contractCount,
            );
			$handle['contract'] = Db::name('Contract')->where([['', 'exp', Db::raw("FIND_IN_SET('{$this->uid}',check_admin_ids)")],['delete_time', '=', 0]])->count();
        }
        if (in_array('project', $module)) {
			
			$project_ids = Db::name('ProjectUser')->where(['uid' => $this->uid, 'delete_time' => 0])->column('project_id');
			$whereProject = [];
			$whereProject[] = ['delete_time', '=', 0];
			$whereProject[] = ['id', 'in', $project_ids];			
            $projectCount = Db::name('Project')->where($whereProject)->count();
			
			$whereOr = array();
			$map1 = [];
			$map2 = [];
			$map3 = [];
			$map4 = [];
			$uid = $this->uid;
			$map1[] = ['admin_id', '=', $uid];
            $map2[] = ['director_uid', '=', $uid];
            $map3[] = ['', 'exp', Db::raw("FIND_IN_SET({$uid},assist_admin_ids)")];
            $map4[] = ['project_id', 'in', $project_ids];
			
			$whereOr =[$map1,$map2,$map3,$map4];
            $taskCount = Db::name('ProjectTask')
				->where(function ($query) use ($whereOr) {
					if (!empty($whereOr))
						$query->whereOr($whereOr);
					})
				->where([['delete_time', '=', 0]])->count();
			
            $total[] = array(
                'name' => '项目',
                'num' => $projectCount,
            );
            $total[] = array(
                'name' => '任务',
                'num' => $taskCount,
            );
			$handle['task'] = Db::name('ProjectTask')->where([['director_uid', '=', $this->uid],['flow_status', '<', 3],['delete_time', '=', 0]])->count();
        }
        if (in_array('article', $module)) {
            $articleCount = Db::name('Article')->where([['delete_time', '=', 0],['uid', '=', $this->uid]])->count();
            $total[] = array(
                'name' => '文章',
                'num' => $articleCount,
            );
        }
		
		$adminGroup = Db::name('PositionGroup')->where(['pid' => $this->pid])->column('group_id');
		$adminLayout = Db::name('AdminGroup')->where('id', 'in', $adminGroup)->column('layouts');
		$adminLayouts = [];
		foreach ($adminLayout as $k => $v) {
			$v = explode(',', $v);
			$adminLayouts = array_merge($adminLayouts, $v);
		}
		$layouts = get_config('layout');
		$layout_selected = [];
		foreach ($layouts as $key =>$vo) {
			if (!empty($adminLayouts) and in_array($vo['id'], $adminLayouts)) {
				$layout_selected[] = $vo;
			}
		}
		View::assign('layout_selected',$layout_selected);
        View::assign('total', $total);
        View::assign('handle', $handle);
        View::assign('install', $install);
        View::assign('TP_VERSION', \think\facade\App::version());
        return View();
    }
	
	//通讯录
	public function mail_list()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['id|username|name|nickname|mobile|desc', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['status', '=', 1];
            if (!empty($param['did'])) {
                $department_array = get_department_son($param['did']);
                $where[] = ['did', 'in', $department_array];
            }
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $admin = \app\user\model\Admin::where($where)
                ->order('id desc')
                ->paginate($rows, false, ['query' => $param])
                ->each(function ($item, $key) {
                    $item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
                    $item->position = Db::name('Position')->where(['id' => $item->position_id])->value('title');
					if($item->is_hide ==1){
						$item->mobile = hidetel($item->mobile);
						$item->email = hidetel($item->email);
					}
                    $item->entry_time = empty($item->entry_time) ? '-' : date('Y-m-d', $item->entry_time);
                });
            return table_assign(0, '', $admin);
        } else {
            return view();
        }
    }
	

    //修改个人信息
    public function edit_personal()
    {
		if (request()->isAjax()) {
            $param = get_params();
            $uid = $this->uid;
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($param);
            return to_assign();
        }
		else{
			View::assign('admin',get_admin($this->uid));
			return view();
		}
    }

    //修改密码
    public function edit_password()
    {
		if (request()->isAjax()) {
			
			//下面部分代码可删除--------------
			if($_SERVER['HTTP_HOST']=='oa.gougucms.com'){
				return to_assign(1, 'Bad Man，为什么总想着改别人的密码？已记录IP，抓住你了！');
			}
			//上面部分代码可删除--------------
			
			$param = get_params();			
            try {
                validate(AdminCheck::class)->scene('editPwd')->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $uid = $this->uid;
			
			$admin = Db::name('Admin')->where(['id' => $uid])->find();
			$old_psw = set_password($param['old_pwd'], $admin['salt']);
			if ($admin['pwd'] != $old_psw) {
				return to_assign(1, '旧密码错误');
			}

			$salt = set_salt(20);
			$new_pwd = set_password($param['pwd'], $salt);
			$data = [
				'reg_pwd' => '',
				'salt' => $salt,
				'pwd' => $new_pwd,
				'update_time' => time(),
			];
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($data);
            return to_assign();
        }
		else{
			View::assign('admin',get_admin($this->uid));
			return view();
		}
    }
	
    //系统操作日志
    public function log_list()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$log = new AdminLog();
			$content = $log->get_log_list($param);
			return table_assign(0, '', $content);
		}else{
			return view();
		}
    }
	
	//设置theme
	public function set_theme()
    {
        if (request()->isAjax()) {
            $param = get_params();
			Db::name('Admin')->where('id',$this->uid)->update(['theme'=>$param['theme']]);
            return to_assign();
        }
		else{
			return to_assign(1,'操作错误');
		}
    }
}
