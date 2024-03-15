<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\message\controller;

use app\base\BaseController;
use app\message\model\Message as MessageList;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{	
    //收件箱
    public function inbox()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $param['status'] = 1;
            $map = [];
            if (!empty($param['keywords'])) {
                $map[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['read'])) {
				if($param['read']==1){
					$map[] = ['read_time', '=', 0];
				}else{
					$map[] = ['read_time', '>', 0];
				}                
            }
            if (!empty($param['template'])) {
				if($param['template']==0){
					$map[] = ['template', '=', 0];
				}else{
					$map[] = ['template', '>', 0];
				}
            }
            $map[] = ['to_uid', '=', $this->uid];
            $map[] = ['status', '=', $param['status']];
            //按时间检索
            $start_time = isset($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = isset($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $map[] = ['send_time', 'between', "$start_time,$end_time"];
            }
			$model = new MessageList();
            $list = $model->get_list($param,$map,$this->uid);
            return table_assign(0, '', $list);
        } else {
			$where1 = [['from_uid', '=', $this->uid]];
			$where2 = [['to_uid', '=', $this->uid]];
			$count = [
				'inbox' => MessageList::where([['to_uid', '=', $this->uid],['status', '=', 1]])->count(),
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['to_uid', '=', 0],['is_draft', '=', 1],['status', '=', 1]])->count(),
				'draft' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['status', '=', 1]])->count(),
				'rubbish' => MessageList::where([['status', '=', 0]])->where(function($query) use ($where1,$where2) {
								$query->where($where1)->whereor($where2);
							})->count()
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
            return view();
        }
    }
    //发件箱
    public function sendbox()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $param['status'] = 1;
            $map = [];
            if (!empty($param['keywords'])) {
                $map[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            $map[] = ['from_uid', '=', $this->uid];
            $map[] = ['to_uid', '=', 0];
            $map[] = ['status', '=', $param['status']];
            $map[] = ['is_draft', '=', 1];
            //按时间检索
            $start_time = isset($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = isset($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $map[] = ['send_time', 'between', "$start_time,$end_time"];
            }
            $model = new MessageList();
            $list = $model->get_list($param,$map,$this->uid);
            return table_assign(0, '', $list);
        } else {
			$where1 = [['from_uid', '=', $this->uid]];
			$where2 = [['to_uid', '=', $this->uid]];
			$count = [
				'inbox' => MessageList::where([['to_uid', '=', $this->uid],['status', '=', 1]])->count(),
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['to_uid', '=', 0],['is_draft', '=', 1],['status', '=', 1]])->count(),
				'draft' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['status', '=', 1]])->count(),
				'rubbish' => MessageList::where([['status', '=', 0]])->where(function($query) use ($where1,$where2) {
								$query->where($where1)->whereor($where2);
							})->count()
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
            return view();
        }
    }

    //草稿箱
    public function draft()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $param['status'] = 2;
            $map = [];
            if (!empty($param['keywords'])) {
                $map[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            $map[] = ['from_uid', '=', $this->uid];
            $map[] = ['status', '=', 1];
            $map[] = ['is_draft', '=', $param['status']];
            //按时间检索
            $start_time = isset($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = isset($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $map[] = ['send_time', 'between', "$start_time,$end_time"];
            }
            $model = new MessageList();
            $list = $model->get_list($param,$map,$this->uid);
            return table_assign(0, '', $list);
        } else {
			$where1 = [['from_uid', '=', $this->uid]];
			$where2 = [['to_uid', '=', $this->uid]];
			$count = [
				'inbox' => MessageList::where([['to_uid', '=', $this->uid],['status', '=', 1]])->count(),
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['to_uid', '=', 0],['is_draft', '=', 1],['status', '=', 1]])->count(),
				'draft' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['status', '=', 1]])->count(),
				'rubbish' => MessageList::where([['status', '=', 0]])->where(function($query) use ($where1,$where2) {
								$query->where($where1)->whereor($where2);
							})->count()
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
            return view();
        }
    }

    //垃圾箱
    public function rubbish()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $param['status'] = 0;
            $map = [];
            if (!empty($param['keywords'])) {
                $map[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            $map[] = ['status', '=', $param['status']];
            //按时间检索
            $start_time = isset($param['start_time']) ? strtotime(urldecode($param['start_time'])) : 0;
            $end_time = isset($param['end_time']) ? strtotime(urldecode($param['end_time'])) : 0;
            if ($start_time > 0 && $end_time > 0) {
                $map[] = ['send_time', 'between', "$start_time,$end_time"];
            }
            $model = new MessageList();
            $list = $model->get_list($param,$map,$this->uid);
            return table_assign(0, '', $list);
        } else {
			$where1 = [['from_uid', '=', $this->uid]];
			$where2 = [['to_uid', '=', $this->uid]];
			$count = [
				'inbox' => MessageList::where([['to_uid', '=', $this->uid],['status', '=', 1]])->count(),
				'sendbox' => MessageList::where([['from_uid', '=', $this->uid],['to_uid', '=', 0],['is_draft', '=', 1],['status', '=', 1]])->count(),
				'draft' => MessageList::where([['from_uid', '=', $this->uid],['is_draft', '=', 2],['status', '=', 1]])->count(),
				'rubbish' => MessageList::where([['status', '=', 0]])->where(function($query) use ($where1,$where2) {
								$query->where($where1)->whereor($where2);
							})->count()
			];
			View::assign('count', $count);
			View::assign('action', $this->action);
            return view();
        }
    }

    //新增信息
    public function add()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $fid = 0;
        if ($id > 0) {
            $model = new MessageList();
			$detail = $model->detail($id);
            if (empty($detail)) {
				throw new \think\exception\HttpException(406, '找不到记录');
            }
			if ($detail['from_uid'] != $this->uid) {
				throw new \think\exception\HttpException(406, '找不到记录');
			}
            $fid = $detail['fid'];
            $person_name = [];
            if ($detail['type'] == 1) { //人员
                $users = Db::name('Admin')->where('status', 1)->where('id', 'in', $detail['type_user'])->select()->toArray();
                $person_name = array_column($users, 'name');
            } elseif ($detail['type'] == 2) { //部门
                $departments = Db::name('Department')->where('id', 'in', $detail['type_user'])->select()->toArray();
                $person_name = array_column($departments, 'title');
            } elseif ($detail['type'] == 3) { //角色
                $group_uid = Db::name('PositionGroup')->where('group_id', 'in', $detail['type_user'])->select()->toArray();
                $pids = array_column($group_uid, 'pid');
                $positions = Db::name('Position')->where('id', 'in', $pids)->select()->toArray();
                $person_name = array_column($positions, 'title');
            }
            $detail['person_name'] = implode(",", $person_name);
            View::assign('detail', $detail);
        }
        View::assign('id', $id);
        View::assign('fid', $fid);
        return view();
    }

    //回复和转发消息
    public function reply()
    {
        $id = empty(get_params('id')) ? 0 : get_params('id');
        $type = empty(get_params('type')) ? 0 : get_params('type');
        $model = new MessageList();
        $detail = $model->detail($id);
        if (empty($detail)) {
			throw new \think\exception\HttpException(406, '找不到记录');
        }
        if ($detail['to_uid'] != $this->uid && $detail['from_uid'] != $this->uid) {
            throw new \think\exception\HttpException(406, '找不到记录');
        }
        $sender = get_admin($detail['from_uid']);
        $detail['person_name'] = $sender['name'];
        View::assign('detail', $detail);
        View::assign('fid', $id);
        View::assign('type', $type);
        return view();
    }

    //查看消息
    public function read()
    {
        $param = get_params();
        $id = $param['id'];
		$model = new MessageList();
        $detail = $model->detail($id);
        if (empty($detail)) {
            throw new \think\exception\HttpException(406, '找不到记录');
        }
        if ($detail['to_uid'] != $this->uid && $detail['from_uid'] != $this->uid) {
            throw new \think\exception\HttpException(406, '找不到记录');
        }
        Db::name('Message')->where(['id' => $id])->update(['read_time' => time()]);
        if($detail['from_uid']==0){
            $detail['person_name'] = '系统管理员';
        }
        else{
            $sender = get_admin($detail['from_uid']);
            $detail['person_name'] = $sender['name'];
        }
		if($detail['send_time']>0){
			$detail['send_time'] = date('Y-m-d H:i:s',$detail['send_time']);    
		}
		else{
			$detail['send_time'] = '-';
		}        
        //发送人查询
        $user_names=[];
        //已读回执
        $read_user_names = [];
		
        if($detail['from_uid'] == $this->uid){
            $mails= MessageList::where(['pid' => $id])->select()->toArray();
            $read_mails= MessageList::where([['pid','=',$id],['read_time','>',2]])->select()->toArray();
            $read_user_ids = array_column($read_mails, 'to_uid');
            $read_users = Db::name('Admin')->where('status', 1)->where('id', 'in', $read_user_ids)->select()->toArray();
            $read_user_names = array_column($read_users, 'name');
			
			$user_ids = array_column($mails, 'to_uid');
			$users = Db::name('Admin')->where('status', 1)->where('id', 'in', $user_ids)->select()->toArray();
			$user_names = array_column($users, 'name');
        }
		else{
			$users = Db::name('Admin')->where('id', $detail['to_uid'])->value('name');
			array_push($user_names,$users);
		}
        $detail['users'] = implode(",", $user_names);
        $detail['read_users'] = implode(",", $read_user_names);
        View::assign('detail', $detail);
        return view();
    }

    //保存到草稿
    public function save()
    {
        $param = get_params();
        $id = empty($param['id']) ? 0 : $param['id'];
        $fid = empty($param['fid']) ? 0 : $param['fid'];
        //接受人类型判断
        if ($param['type'] == 1) {
            if (!$param['uids']) {
                return to_assign(1, '人员不能为空');
            } else {
                $type_user = $param['uids'];
            }
        } elseif ($param['type'] == 2) {
            if (!$param['dids']) {
                return to_assign(1, '部门不能为空');
            } else {
                $type_user = $param['dids'];
            }
        } elseif ($param['type'] == 3) {
            if (!$param['pids']) {
                return to_assign(1, '岗位不能为空');
            } else {
                $type_user = $param['pids'];
            }
        } else {
            $type_user = '';
        }
        //基础信息数据
        $admin_id = $this->uid;
        $basedata = [];
        $basedata['from_uid'] = $admin_id;
        $basedata['fid'] = $fid;
        $basedata['is_draft'] = 2;//默认是草稿信息
        $basedata['title'] = $param['title'];
        $basedata['type'] = $param['type'];
        $basedata['type_user'] = $type_user;
        $basedata['content'] = $param['content'];
        $basedata['file_ids'] = $param['file_ids'];
		$basedata['controller_name'] = $this->controller;
        $basedata['module_name'] = $this->module;
        $basedata['action_name'] = $this->action;
        if ($id > 0) {
            //编辑信息的情况
            $basedata['update_time'] = time();
            $basedata['id'] = $id;
            $res = MessageList::strict(false)->field(true)->update($basedata);
        } else {
            //新增信息的情况
            $basedata['create_time'] = time();
            $res = MessageList::strict(false)->field(true)->insertGetId($basedata);
        }
        if ($res !== false) {
            //信息附件处理
            if ($id > 0) {
                $mid = $id;
            } else {
                $mid = $res;
            }
            add_log('save',$mid,$basedata,'消息');
            return to_assign(0, '保存成功', $mid);
        } else {
            return to_assign(1, '操作失败');
        }
    }

    //发送消息
    public function send()
    {
        $param = get_params();
        //查询要发的消息
        $msg = MessageList::where(['id' => $param['id']])->find();
        $users = [];
        if ($msg) {
            $admin_id = $msg['from_uid'];
            //查询全部收件人
            if ($msg['type'] == 1) { //人员
                $users = Db::name('Admin')->where('status', 1)->where('id', 'in', $msg['type_user'])->select()->toArray();
            } elseif ($msg['type'] == 2) { //部门
                $users = Db::name('Admin')->where('status', 1)->where('did', 'in', $msg['type_user'])->select()->toArray();
            } elseif ($msg['type'] == 3) { //角色
                $group_uid = Db::name('PositionGroup')->where('group_id', 'in', $msg['type_user'])->select()->toArray();
                $pids = array_column($group_uid, 'pid');
                $users = Db::name('Admin')->where('status', 1)->where('position_id', 'in', $pids)->select()->toArray();
            } elseif ($msg['type'] == 4) { //全部
                $users = Db::name('Admin')->where('status', 1)->select()->toArray();
            }
            //组合要发的消息
            $send_data = [];
            foreach ($users as $key => $value) {
                if (!$value || ($value['id'] == $admin_id)) {
                    continue;
                }
                $send_data[] = array(
					'pid' => $param['id'],//来源发件箱关联id
					'to_uid' => $value['id'],//接收人
					'fid' => $msg['fid'],//转发或回复消息关联id
					'title' => $msg['title'],
					'content' => $msg['content'],
					'file_ids' => $msg['file_ids'],
					'type' => $msg['type'],//接收人类型
					'type_user' => $msg['type_user'],//接收人数据
					'from_uid' => $this->uid,//发送人
					'controller_name' => $this->controller,
					'module_name' => $this->module,
					'action_name' => $this->action,
					'send_time' => time(),
					'create_time' => time()
                );
            }
            $res = MessageList::strict(false)->field(true)->insertAll($send_data);
            if ($res!==false) {
                //草稿消息变成已发消息
                MessageList::where(['id' => $msg['id']])->update(['is_draft' => '1', 'send_time' => time(), 'update_time' => time()]);
                add_log('send',$msg['id'],[],'消息');
                return to_assign(0, '发送成功');
            } else {
                return to_assign(1, '发送失败');
            }
        } else {
            return to_assign(1, '发送失败，找不到要发送的内容');
        }
    }

    //状态修改
    public function check()
    {
        $param = get_params();
        $type = empty($param['type']) ? 0 : $param['type'];
        $source = empty($param['source']) ? 0 : $param['source'];
        $ids = empty($param['ids']) ? 0 : $param['ids'];
        $idArray = explode(',', $ids);
        $list = [];
        foreach ($idArray as $key => $val) {
            if ($type==1) { //设置信息为已读
                $list[] = [
                    'read_time' => time(),
                    'id' => $val,
                ];
            }
            else if ($type==2) {  //设置信息进入垃圾箱
                $list[] = [
                    'status' => 0,
                    'id' => $val,
                    'delete_source' => $source,
                    'update_time' => time(),
                ];
            }
            else if ($type==3) {  //设置信息从垃圾箱恢复
                $list[] = [
                    'status' => 1,
                    'id' => $val,
                    'update_time' => time(),
                ];
            }
            else if ($type==4) {  //设置信息彻底删除
                $list[] = [
                    'status' => -1,
                    'id' => $val,
                    'update_time' => time(),
                ];
            }

        }
        foreach ($list as $key => $v) {
            if (MessageList::update($v) !== false) {
                if ($type = 1) {
                    add_log('view', $v['id'],[],'消息');
                } else if ($type = 2) {
                    add_log('delete', $v['id'],[],'消息');
                } else if ($type = 3) {
                    add_log('recovery', $v['id'],[],'消息');
                }
                else if ($type = 4) {
                    add_log('clear', $v['id'],[],'消息');
                }
            }
        }
        return to_assign(0, '操作成功');
    }

}
