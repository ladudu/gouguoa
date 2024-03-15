<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Index extends BaseController
{
 //上传文件
 public function upload()
 {
     $param = get_params();
     //var_dump($param);exit;
     $sourse = 'file';
     if(isset($param['sourse'])){
         $sourse = $param['sourse'];
     }
     if($sourse == 'file' || $sourse == 'tinymce'){
         if(request()->file('file')){
             $file = request()->file('file');
         }
         else{
             return to_assign(1, '没有选择上传文件');
         }
     }
     else{
         if (request()->file('editormd-image-file')) {
             $file = request()->file('editormd-image-file');
         } else {
             return to_assign(1, '没有选择上传文件');
         }
     }
     // 获取上传文件的hash散列值
     $sha1 = $file->hash('sha1');
     $md5 = $file->hash('md5');
     $rule = [
            'image' => 'jpg,png,jpeg,gif',
            'doc' => 'txt,doc,docx,ppt,pptx,xls,xlsx,pdf',
            'file' => 'zip,gz,7z,rar,tar',
            'video' => 'mpg,mp4,mpeg,avi,wmv,mov,flv,m4v',
        ];
        $fileExt = $rule['image'] . ',' . $rule['doc'] . ',' . $rule['file'] . ',' . $rule['video'];
        //1M=1024*1024=1048576字节
        $fileSize = 100 * 1024 * 1024;
     if (isset($param['type']) && $param['type']) {
         $fileExt = $rule[$param['type']];
     }
     if (isset($param['size']) && $param['size']) {
         $fileSize = $param['size'];
     }
     $validate = \think\facade\Validate::rule([
         'image' => 'require|fileSize:' . $fileSize . '|fileExt:' . $fileExt,
     ]);
     $file_check['image'] = $file;
     if (!$validate->check($file_check)) {
         return to_assign(1, $validate->getError());
     }
     // 日期前綴
     $dataPath = date('Ym');
     $use = 'thumb';
     $filename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file, function () use ($md5) {
         return $md5;
     });
     if ($filename) {
         //写入到附件表
         $data = [];
         $path = get_config('filesystem.disks.public.url');
         $data['filepath'] = $path . '/' . $filename;
         $data['name'] = $file->getOriginalName();
         $data['mimetype'] = $file->getOriginalMime();
         $data['fileext'] = $file->extension();
         $data['filesize'] = $file->getSize();
         $data['filename'] = $filename;
         $data['sha1'] = $sha1;
         $data['md5'] = $md5;
         $data['module'] = \think\facade\App::initialize()->http->getName();
         $data['action'] = app('request')->action();
         $data['uploadip'] = app('request')->ip();
         $data['create_time'] = time();
         $data['user_id'] = $this->uid;
         if ($data['module'] = 'admin') {
             //通过后台上传的文件直接审核通过
             $data['status'] = 1;
             $data['admin_id'] = $data['user_id'];
             $data['audit_time'] = time();
         }
         $data['use'] = request()->has('use') ? request()->param('use') : $use; //附件用处
         $res['id'] = Db::name('file')->insertGetId($data);
         $res['filepath'] = $data['filepath'];
         $res['name'] = $data['name'];
         $res['filename'] = $data['filename'];
         $res['filesize'] = $data['filesize'];
         $res['fileext'] = $data['fileext'];
         add_log('upload', $data['user_id'], $data,'文件');
         if($sourse == 'editormd'){
             //editormd编辑器上传返回
             return json(['success'=>1,'message'=>'上传成功','url'=>$data['filepath']]);
         }
         else if($sourse == 'tinymce'){
             //tinymce编辑器上传返回
             return json(['success'=>1,'message'=>'上传成功','location'=>$data['filepath']]);
         }
         else{
             //普通上传返回
             return to_assign(0, '上传成功', $res);
         }            
     } else {
         return to_assign(1, '上传失败，请重试');
     }
 }

    //清空缓存
    public function cache_clear()
    {
        \think\facade\Cache::clear();
        return to_assign(0, '系统缓存已清空');
    }

    // 测试邮件发送
    public function email_test()
    {
        $sender = get_params('email');
        //检查是否邮箱格式
        if (!is_email($sender)) {
            return to_assign(1, '测试邮箱码格式有误');
        }
        $email_config = \think\facade\Db::name('config')->where('name', 'email')->find();
        $config = unserialize($email_config['content']);
        $content = $config['template'];
        //所有项目必须填写
        if (empty($config['smtp']) || empty($config['smtp_port']) || empty($config['smtp_user']) || empty($config['smtp_pwd'])) {
            return to_assign(1, '请完善邮件配置信息');
        }

        $send = send_email($sender, '测试邮件', $content);
        if ($send) {
            return to_assign(0, '邮件发送成功');
        } else {
            return to_assign(1, '邮件发送失败');
        }
    }

    //获取部门
    public function get_department()
    {
        $department = get_department();
        return to_assign(0, '', $department);
    }

    //获取部门树形节点列表
    public function get_department_tree()
    {
        $department = get_department();
        $list = get_tree($department, 0, 2);
        $data['trees'] = $list;
        return json($data);
    }
	
    //获取部门树形节点列表2
    public function get_department_select()
    {
		$keyword = get_params('keyword');
		$selected = [];
		if(!empty($keyword)){
			$selected = explode(",",$keyword);
		}		
        $department = get_department();
        $list = get_select_tree($department, 0,0,$selected);
		return to_assign(0, '',$list);
    }

    //获取子部门所有员工
    public function get_employee($did = 0)
    {
        $did = get_params('did');
		if($did == 1){
			$department = $did;
		}
		else{
			$department = get_department_son($did);
		}        
        $employee = Db::name('admin')
            ->field('a.id,a.did,a.position_id,a.mobile,a.name,a.nickname,a.sex,a.status,a.thumb,a.username,d.title as department')
            ->alias('a')
            ->join('Department d', 'a.did = d.id')
            ->where(['a.status' => 1])
            ->where('a.id', ">", 1)
            ->where('a.did', "in", $department)
            ->select();
        return to_assign(0, '', $employee);
    }
	
    //获取所有员工
    public function get_personnel()
    {       
		$param = get_params();
		$where[] = ['a.status', '=', 1];
		$where[] = ['a.id', '>', 1];
		if (!empty($param['keywords'])) {
			$where[] = ['a.name', 'like', '%' . $param['keywords'] . '%'];
		}
		if(!empty($param['ids'])){
			$where[] = ['a.id', 'notin', $param['ids']];
		}
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Db::name('admin')
            ->field('a.id,a.did,a.position_id,a.mobile,a.name,a.nickname,a.sex,a.status,a.thumb,a.username,d.title as department')
            ->alias('a')
            ->join('Department d', 'a.did = d.id')
            ->where($where)
			->order('a.id desc')
			->paginate($rows, false, ['query' => $param]);
		return table_assign(0, '', $list);
    }
	
    //获取部门所有员工
    public function get_employee_select()
    {
		$keyword = get_params('keyword');
		$selected = [];
		if(!empty($keyword)){
			$selected = explode(",",$keyword);
		}
        $employee = Db::name('admin')
            ->field('id as value,name')
            ->where(['status' => 1])
            ->select()->toArray();
			
		foreach($employee as $k => &$v){	
			$v['selected'] = '';
			if(in_array($v['value'],$selected)){
				$v['selected'] = 'selected';
			}
		}
        return to_assign(0, '', $employee);
    }

    //获取角色列表
    public function get_position()
    {
        $position = Db::name('Position')->field('id,title as name')->where([['status', '=', 1], ['id', '>', 1]])->select();
        return to_assign(0, '', $position);
    }
	
	//获取审核类型
    public function get_flow_cate($type=0)
    {
		$flows = Db::name('FlowType')->where(['type'=>$type,'status'=>1])->select()->toArray();
		return to_assign(0, '', $flows);
	}
	//获取审核步骤人员
    public function get_flow_users($id=0)
    {
        $flow = Db::name('Flow')->where(['id' => $id])->find();
        $flowData = unserialize($flow['flow_list']);
		if(!empty($flowData)){
			foreach ($flowData as $key => &$val) {
				$val['user_id_info'] = Db::name('Admin')->field('id,name,thumb')->where('id','in',$val['flow_uids'])->select()->toArray();
			}
		}
		$data['copy_uids'] = $flow['copy_uids'];
		$data['copy_unames'] ='';
		if($flow['copy_uids']!=''){
			$copy_unames = Db::name('Admin')->where('id', 'in', $flow['copy_uids'])->column('name');
			$data['copy_unames'] = implode(',', $copy_unames);
		}
		$data['flow_data'] = $flowData;
        return to_assign(0, '', $data);
    }
	
	//获取审核流程节点
    public function get_flow_nodes($id=0,$type=1)
    {
		$flows = Db::name('FlowStep')->where(['action_id'=>$id,'type'=>$type,'delete_time'=>0])->order('sort asc')->select()->toArray();
		foreach ($flows as $key => &$val) {
            $user_id_info = Db::name('Admin')->field('id,name,thumb')->where('id','in',$val['flow_uids'])->select()->toArray();						
			foreach ($user_id_info as $k => &$v) {
				$v['check_time'] = 0;
				$v['content'] = '';
				$v['status'] = 0;			
				$check_array = Db::name('FlowRecord')->where(['check_user_id' => $v['id'],'step_id' => $val['id']])->order('check_time desc')->select()->toArray();
				if(!empty($check_array)){
					$checked = $check_array[0];
					$v['check_time'] = date('Y-m-d H:i', $checked['check_time']);
					$v['content'] = $checked['content'];
					$v['status'] = $checked['status'];	
				}
			}
			
			$check_list = Db::name('FlowRecord')
						->field('f.*,a.name,a.thumb')
						->alias('f')
						->join('Admin a', 'a.id = f.check_user_id', 'left')
						->where(['f.step_id' => $val['id']])->select()->toArray();
			foreach ($check_list as $kk => &$vv) {		
				$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
			}
			
			$val['user_id_info'] = $user_id_info;
			$val['check_list'] = $check_list;
        }
        return to_assign(0, '', $flows);
    }
	
	//获取审核流程节点
    public function get_flow_record($id=0,$type=1)
    {			
		$check_list = Db::name('FlowRecord')
					->field('f.*,a.name,a.thumb')
					->alias('f')
					->join('Admin a', 'a.id = f.check_user_id', 'left')
					->where(['f.action_id'=>$id,'f.type'=>$type])
					->order('check_time asc')
					->select()->toArray();
		foreach ($check_list as $kk => &$vv) {		
			$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
		}
        return to_assign(0, '', $check_list);
    }
	
    //流程审核
    public function flow_check()
    {
        $param = get_params();
		$id = $param['id'];
		$type = $param['type'];
		$detail = [];
		$subject = '一个审批';
		if($type==1){
			//日常审核
			$detail = Db::name('Approve')->where(['id' => $id])->find();
			$subject = '一个日常审批';
			$msg_title_type = $detail['type'];
		}
		else if($type==2){
			//报销审核
			$detail = Db::name('Expense')->where(['id' => $id])->find();
			$subject = '一个报销审批';
			$msg_title_type = 22;
		}
		else if($type==3){
			//发票审核
			$detail = Db::name('Invoice')->where(['id' => $id])->find();
			$subject = '一个发票审批';
			$msg_title_type = 23;
		}
		else if($type==4){
			//合同审核
			$detail = Db::name('Contract')->where(['id' => $id])->find();
			$subject = '一个合同审批';
			$msg_title_type = 24;
		}
		if (empty($detail)){		
			return to_assign(1,'审批数据错误');
		}
		//当前审核节点详情
		$step = Db::name('FlowStep')->where(['action_id'=>$id,'type'=>$type,'sort'=>$detail['check_step_sort'],'delete_time'=>0])->find();
		
		//审核通过
		if($param['check'] == 1){
			$check_admin_ids = explode(",", strval($detail['check_admin_ids']));
			if (!in_array($this->uid, $check_admin_ids)){		
				return to_assign(1,'您没权限审核该审批');
			}
			
			//多人会签审批
			if($step['flow_type'] == 4){
				//查询当前会签记录数
				$check_count = Db::name('FlowRecord')->where(['action_id'=>$id,'type'=>$type,'step_id'=>$step['id']])->count();
				//当前会签记应有记录数
				$flow_count = explode(',', $step['flow_uids']);
				if(($check_count+1) >=count($flow_count)){
					$next_step = Db::name('FlowStep')->where(['action_id'=>$id,'type'=>$type,'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
					if($next_step){
						//存在下一步审核
						if($next_step['flow_type'] == 1){
							$param['check_admin_ids'] = get_department_leader($detail['admin_id']);
						}
						else if($next_step['flow_type'] == 2){
							$param['check_admin_ids'] = get_department_leader($detail['admin_id'],1);
						}
						else{
							$param['check_admin_ids'] = $next_step['flow_uids'];
						}
						$param['check_step_sort'] = $detail['check_step_sort']+1;
						$param['check_status'] = 1;
					}
					else{
						//不存在下一步审核，审核结束
						$param['check_status'] = 2;
						$param['check_admin_ids'] ='';
					}
				}
				else{
					$param['check_status'] = 1;
					$param['check_admin_ids'] = $step['flow_uids'];
				}
			}
			else if($step['flow_type'] == 0){
				//自由人审批
				if($param['check_node'] == 2){
					$next_step = $detail['check_step_sort']+1;
					$flow_step = array(
						'action_id' => $id,
						'sort' => $next_step,
						'type' => $type,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					$fid = Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
					//下一步审核步骤
					$param['check_admin_ids'] = $param['check_admin_ids'];
					$param['check_step_sort'] = $next_step;
					$param['check_status'] = 1;
				}
				else{
					//不存在下一步审核，审核结束
					$param['check_status'] = 2;
					$param['check_admin_ids'] ='';
				}
			}
			else{
				$next_step = Db::name('FlowStep')->where(['action_id'=>$id,'type'=>$type,'sort'=>($detail['check_step_sort']+1),'delete_time'=>0])->find();
				if($next_step){
					//存在下一步审核
					if($next_step['flow_type'] == 1){
						$param['check_admin_ids'] = get_department_leader($detail['admin_id']);
					}
					else if($next_step['flow_type'] == 2){
						$param['check_admin_ids'] = get_department_leader($detail['admin_id'],1);
					}
					else{
						$param['check_admin_ids'] = $next_step['flow_uids'];
					}
					$param['check_step_sort'] = $detail['check_step_sort']+1;
					$param['check_status'] = 1;
				}
				else{
					//不存在下一步审核，审核结束
					$param['check_status'] = 2;
					$param['check_admin_ids'] ='';
				}
			}
			if($param['check_status'] == 1 && empty($param['check_admin_ids'])){
				return to_assign(1,'找不到下一步的审批人，该审批流程设置有问题，请联系HR或者管理员');
			}			
			//审核通过数据操作
			$param['last_admin_id'] = $this->uid;
			$param['flow_admin_ids'] = $detail['flow_admin_ids'].$this->uid.',';
			
			if($type==1){
				//日常审核
				$res = Db::name('Approve')->strict(false)->field('check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			}
			else if($type==2){
				//报销审核
				$res = Db::name('Expense')->strict(false)->field('check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			}
			else if($type==3){
				//发票审核
				$res = Db::name('Invoice')->strict(false)->field('check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			}
			else if($type==4){
				//合同审核
				$res = Db::name('Contract')->strict(false)->field('check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			}
			
			if($res!==false){
				$checkData=array(
					'action_id' => $id,
					'step_id' => $step['id'],
					'check_user_id' => $this->uid,
					'type' => $type,
					'check_time' => time(),
					'status' => $param['check'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('check', $param['id'], $param,$subject);
				//发送消息通知
				$msg=[
					'create_time'=>date('Y-m-d H:i:s',$detail['create_time']),
					'action_id'=>$id,
					'title' => Db::name('FlowType')->where('id',$msg_title_type)->value('title'),
					'from_uid'=>$detail['admin_id']
				];
				if($param['check_status'] == 1){
					$users = $param['check_admin_ids'];
					sendMessage($users,($type*10+11),$msg);
				}
				if($param['check_status'] == 2){
					$users = $detail['admin_id'];
					sendMessage($users,($type*10+12),$msg);
				}
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}
		}
		else if($param['check'] == 2){
			$check_admin_ids = explode(",", strval($detail['check_admin_ids']));
			if (!in_array($this->uid, $check_admin_ids)){		
				return to_assign(1,'您没权限审核该审批');
			}
			//拒绝审核，数据操作
			$param['check_status'] = 3;
			$param['last_admin_id'] = $this->uid;
			$param['flow_admin_ids'] = $detail['flow_admin_ids'].$this->uid.',';
			$param['check_admin_ids'] ='';
			if($step['flow_type'] == 5){
				//获取上一步的审核信息
				$prev_step = Db::name('FlowStep')->where(['action_id'=>$id,'type'=>$type,'sort'=>($detail['check_step_sort']-1),'delete_time'=>0])->find();
				if($prev_step){
					//存在上一步审核
					$param['check_step_sort'] = $prev_step['sort'];
					$param['check_admin_ids'] = $prev_step['flow_uids'];
					$param['check_status'] = 1;
				}
				else{
					//不存在上一步审核，审核初始化步骤
					$param['check_step_sort'] = 0;
					$param['check_admin_ids'] = '';					
					$param['check_status'] = 0;
				}
			}
			if($type==1){
				//日常审核
				$res = Db::name('Approve')->strict(false)->field('check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			}
			else if($type==2){
				//报销审核
				$res = Db::name('Expense')->strict(false)->field('check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			}
			else if($type==3){
				//发票审核
				$res = Db::name('Invoice')->strict(false)->field('check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			}
			else if($type==4){
				//合同审核
				$res = Db::name('Contract')->strict(false)->field('check_step_sort,check_status,last_admin_id,flow_admin_ids,check_admin_ids')->update($param);
			}
			if($res!==false){
				$checkData=array(
					'action_id' => $id,
					'step_id' => $step['id'],
					'check_user_id' => $this->uid,
					'type' => $type,
					'check_time' => time(),
					'status' => $param['check'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('refue', $param['id'], $param,$subject);
				//发送消息通知
				$msg=[
					'create_time'=>date('Y-m-d H:i:s',$detail['create_time']),
					'action_id'=>$detail['id'],
					'title' => Db::name('FlowType')->where('id',$msg_title_type)->value('title'),
					'from_uid'=>$detail['admin_id']
				];
				$users = $detail['admin_id'];
				sendMessage($users,($type*10+13),$msg);
				return to_assign();
			}
			else{
				return to_assign(1,'操作失败');
			}			
		}		
		else if($param['check'] == 3){
			if($detail['admin_id'] != $this->uid){
				return to_assign(1,'你没权限操作');
			}
			//撤销审核，数据操作
			$param['check_status'] = 4;
			$param['check_admin_ids'] ='';
			$param['check_step_sort'] =0;
			if($type==1){
				//日常审核
				$res = Db::name('Approve')->strict(false)->field('check_step_sort,check_status,check_admin_ids')->update($param);
			}
			else if($type==2){
				//报销审核
				$res = Db::name('Expense')->strict(false)->field('check_step_sort,check_status,check_admin_ids')->update($param);
			}
			else if($type==3){
				//发票审核
				$res = Db::name('Invoice')->strict(false)->field('check_step_sort,check_status,check_admin_ids')->update($param);
			}
			else if($type==4){
				//合同审核
				$res = Db::name('Contract')->strict(false)->field('check_step_sort,check_status,check_admin_ids')->update($param);
			}
			if($res!==false){
				$checkData=array(
					'action_id' => $id,
					'step_id' => 0,
					'check_user_id' => $this->uid,
					'type' => $type,
					'check_time' => time(),
					'status' => $param['check'],
					'content' => $param['content'],
					'create_time' => time()
				);	
				$aid = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('back', $param['id'], $param,$subject);
				return to_assign();
			}else{
				return to_assign(1,'操作失败');
			}
		}		
    }

    //获取关键字
    public function get_keyword_cate()
    {
        $keyword = Db::name('Keywords')->where(['status' => 1])->order('id desc')->select()->toArray();
        return to_assign(0, '', $keyword);
    }
	
	//读取报销类型
	function get_expense_cate()
	{
		$cate = get_expense_cate();
		return to_assign(0, '', $cate);
	}

	//读取费用类型
	function get_cost_cate()
	{
		$cate = get_cost_cate();
		return to_assign(0, '', $cate);
	}

	//读取印章类型
	function get_seal_cate()
	{
		$cate = get_seal_cate();
		return to_assign(0, '', $cate);
	}

	//读取车辆类型
	function get_car_cate()
	{
		$cate = get_car_cate();
		return to_assign(0, '', $cate);
	}

	//读取企业主体
	function get_subject()
	{
		$subject = get_subject();
		return to_assign(0, '', $subject);
	}

	//读取行业类型
	function get_industry()
	{
		$industry = get_industry();
		return to_assign(0, '', $industry);
	}

	//读取服务类型
	function get_services()
	{
		$services = get_services();
		return to_assign(0, '', $services);
	}
	
	//获取工作类型列表
    public function get_work_cate()
    {
        $cate = get_work_cate();
        return to_assign(0, '', $cate);
    }
}
