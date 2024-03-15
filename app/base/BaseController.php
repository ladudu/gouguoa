<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\base;

use think\App;
use think\exception\HttpResponseException;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use systematic\Systematic;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->module = strtolower(app('http')->getName());
        $this->controller = strtolower($this->request->controller());
        $this->action = strtolower($this->request->action());
        $this->uid = 0;
        $this->did = 0;
        $this->pid = 0;
        // 控制器初始化
        $this->initialize();
    }
    // 初始化
    protected function initialize()
    {
        // 检测权限
        $this->checkLogin();
    }

    /**
     *验证用户登录
     */
    protected function checkLogin()
    {
        if ($this->controller !== 'login' && $this->controller !== 'captcha') {
            $session_admin = get_config('app.session_admin');
            if (!Session::has($session_admin)) {
                if ($this->request->isAjax()) {
                    return to_assign(404, '请先登录');
                } else {
                    redirect('/home/login/index.html')->send();
                    exit;
                }
            } else {
                $this->uid = Session::get($session_admin);
				$login_admin = Db::name('Admin')->where(['id' => $this->uid])->find();
				$this->did = $login_admin['did'];
				$this->pid = $login_admin['position_id'];
                View::assign('login_admin', $login_admin);				
				$is_lock = $login_admin['is_lock'];
				if($is_lock==1){
					redirect('/home/login/lock.html')->send();
					exit;
				}
                // 验证用户访问权限
                if (($this->module == 'api') || ($this->module == 'message') || ($this->module == 'home' && $this->controller == 'index')) {
					return true;
				}
				else{
					$reg_pwd = $login_admin['reg_pwd'];
					if($reg_pwd!==''){
						redirect('/home/index/edit_password.html')->send();
						exit;
					}
                    if (!$this->checkAuth()) {
                        if ($this->request->isAjax()) {
                            return to_assign(405, '你没有权限,请联系管理员或者HR');
                        } else {
                            echo '<div style="text-align:center;color:red;margin-top:20%;">你没有权限访问，请联系管理员或者人事部</div>';exit;
                        }
                    }
                }
            }
        }
    }

    /**
     * 验证用户访问权限
     * @DateTime 2020-12-21
     * @param    string $controller 当前访问控制器
     * @param    string $action 当前访问方法
     * @return   [type]
     */
    protected function checkAuth()
    {
        //Cache::delete('RulesSrc' . $uid);
		$uid = $this->uid;
		$GOUGU = new Systematic();
        $GOUGU->auth($uid);
		$auth_list_all = Cache::get('RulesSrc0');
        $auth_list = Cache::get('RulesSrc' . $uid);
		
        $pathUrl = $this->module . '/' . $this->controller . '/' . $this->action;
        if (!in_array($pathUrl, $auth_list)) {
            return false;
        } else {
            return true;
        }
    }
}
