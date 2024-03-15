<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\oa\controller;

use app\base\BaseController;
use app\oa\model\Work as WorkList;
use app\oa\model\WorkRecord;
use think\facade\Db;
use think\facade\View;

class Work extends BaseController
{
    //获取接收汇报列表
    public function getList($map = [], $param = [])
    {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$Work = WorkRecord::alias('a')
			->field('a.id,a.from_uid,a.to_uid,a.send_time,a.read_time,w.id as wid,w.type,w.works')
			->rightJoin ('Work w','a.wid = w.id')
			->where($map)
			->order('a.send_time desc')
			->paginate($rows, false, ['query' => $param])
			->each(function ($item, $key) {
				$item->send_time = empty($item->send_time) ? '-' : date('Y-m-d H:i:s', $item->send_time);
				$item->from_name = Db::name('Admin')->where(['id' => $item->from_uid])->value('name');
				$item->to_name = Db::name('Admin')->where(['id' => $item->to_uid])->value('name');
				$item->type_title = WorkRecord::$Type[$item->type];
				$item->files = Db::name('WorkFileInterfix')->where(['wid' => $item->wid])->count();
			});
		return $Work;
    }
	
    //获取发送汇报列表
    public function getSend($map = [], $param = [])
    {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$Work = WorkList::where($map)
			->order('create_time desc')
			->paginate($rows, false, ['query' => $param])
			->each(function ($item, $key) {
				$person_name = Db::name('Admin')->where('status', 1)->where('id', 'in', $item['type_user'])->column('name');
				$item->person_name = implode(",", $person_name);
				$item->type_title = WorkRecord::$Type[$item->type];
				$item->files = Db::name('WorkFileInterfix')->where(['wid' => $item->id])->count();
			});
		return $Work;
    }
	
    //汇报列表
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $map = [];
			if($param['send']==1){
				if (!empty($param['type'])) {
					$map[] = ['type', '=', $param['type']];
				}
				//按时间检索
				if (!empty($param['diff_time'])) {
					$diff_time =explode('~', $param['diff_time']);
					$map[] = ['create_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
				}
				$map[] = ['admin_id', '=', $this->uid];         
				$map[] = ['status', '=', 1];
				$list = $this->getSend($map, $param);
			}
			else{
				if (!empty($param['read'])) {
					if($param['read']==1){
						$map[] = ['a.read_time', '=', 0];
					}else{
						$map[] = ['a.read_time', '>', 0];
					}                
				}
				if (!empty($param['type'])) {
					$map[] = ['w.type', '=', $param['type']];
				}
				$map[] = ['a.to_uid', '=', $this->uid];         
				$map[] = ['a.status', '=', 1];
				//按时间检索
				if (!empty($param['diff_time1'])) {
					$diff_time =explode('~', $param['diff_time1']);
					$map[] = ['send_time', 'between', [strtotime(urldecode($diff_time[0])),strtotime(urldecode($diff_time[1]))]];
				}
				$list = $this->getList($map, $param);
			}            
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //新增&编辑
    public function add()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$param['update_time'] = time();
            $res = Db::name('Work')->strict(false)->field('works,plans,remark,update_time')->update($param);
			if ($res !== false) {
				Db::name('WorkFileInterfix')->where(['wid' => $param['id']])->delete();
				//附件插入附件
				if (!empty($param['file_ids'])) {
					$file_array = explode(',', $param['file_ids']);
					$file_data = array();
					foreach ($file_array as $key => $value) {
						if (!$value) {
							continue;
						}
						$file_data[] = array(                        
							'file_id' => $value,
							'wid' => $param['id'],	
							'admin_id' => $this->uid,
							'create_time' => time()							
						);
					}
					if ($file_data) {
						Db::name('WorkFileInterfix')->insertAll($file_data);
					}
				}
				add_log('edit',$param['id']);
				return to_assign();				
			}
			else{
				return to_assign(1,'操作失败');	
			}
		}
		else{
			$id = empty(get_params('id')) ? 0 : get_params('id');
			$type = empty(get_params('type')) ? 1 : get_params('type');
			if ($id > 0) {
				$detail = Db::name('Work')->where(['id' => $id,'admin_id' => $this->uid])->find();
				if (empty($detail)) {
					echo '<div style="text-align:center;color:red;margin-top:20%;">该汇报不存在</div>';exit;
				}
				$person_name = Db::name('Admin')->where('status', 1)->where('id', 'in', $detail['type_user'])->column('name');
				$detail['person_name'] = implode(",", $person_name);
				$file_array = Db::name('WorkFileInterfix')
					->field('wf.id,wf.wid,wf.file_id,f.name,f.filesize,f.filepath,f.fileext,f.create_time,f.admin_id')
					->alias('wf')
					->join('file f', 'wf.file_id = f.id', 'LEFT')
					->order('wf.create_time desc')
					->where(['wf.wid' => $id])
					->select()->toArray();
				$interfix_ids = array_column($file_array, 'id');
				$detail['file_ids'] = implode(",", $interfix_ids);
				$type = $detail['type'];
				View::assign('file_array', $file_array);
				View::assign('detail', $detail);
			}
			View::assign('id', $id);
			View::assign('type', $type);
			return view();
		}
    }

    //查看消息
    public function read()
    {
        $param = get_params();
        $id = $param['id'];
		$detail = Db::name('Work')->where(['id' => $id,'status' => 1])->find();
		if (empty($detail)) {
			echo '<div style="text-align:center;color:red;margin-top:20%;">该汇报不存在</div>';exit;
		}
		//已读人查询
		$read_user_names = [];
		if($detail['admin_id'] !=$this->uid){
			$record = Db::name('WorkRecord')->where(['wid' => $detail['id'],'to_uid' => $this->uid,'status' => 1])->count();
			if ($record == 0) {
				echo '<div style="text-align:center;color:red;margin-top:20%;">该汇报不存在</div>';exit;
			}
			else{
				Db::name('WorkRecord')->where(['wid' => $detail['id'],'to_uid' => $this->uid,'status' => 1])->update(['read_time' => time()]);
			}
		}
		else{
			$read_user_ids= Db::name('WorkRecord')->where([['wid','=',$detail['id']],['to_uid','>',0],['read_time','>',0]])->column('to_uid');
			$read_user_names = Db::name('Admin')->where('status', 1)->where('id', 'in', $read_user_ids)->column('name');
		}
		$sender = get_admin($detail['admin_id']);
		$detail['person_name'] = $sender['name'];
        $detail['send_time'] = date('Y-m-d H:i:s',$detail['create_time']);    
        //当前消息的附件
        $file_array = Db::name('WorkFileInterfix')
            ->field('wf.id,wf.wid,wf.file_id,f.name,f.filesize,f.filepath,f.fileext,f.create_time,f.admin_id')
            ->alias('wf')
            ->join('file f', 'wf.file_id = f.id', 'LEFT')
            ->order('wf.create_time desc')
            ->where(array('wf.wid' => $detail['id']))
            ->select()->toArray();
        $detail['file_array'] = $file_array;
        //接收人查询
		$user_names = Db::name('Admin')->where('status', 1)->where('id', 'in', $detail['type_user'])->column('name');		
        $detail['users'] = implode(",", $user_names);
        $detail['read_users'] = implode(",", $read_user_names);
        $detail['type_title'] = WorkRecord::$Type[$detail['type']];
		if($detail['update_time']>0){
			$detail['update_time_str'] = date('Y-m-d H:i:s',$detail['update_time']);  
		}
		$comment = Db::name('WorkComment')
			->field('a.*,u.name,u.thumb')
			->alias('a')
			->join('Admin u', 'u.id = a.admin_id')
			->order('a.create_time desc')
			->where(['a.work_id'=>$detail['id'],'a.delete_time' => 0])
			->select()->toArray();
		foreach ($comment as $k => &$v) {
			$v['times'] = time_trans($v['create_time']);
			$v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
			if($v['update_time']>0){
				$v['update_time'] = '，最后编辑时间:'.time_trans($v['update_time']);
			}
			else{
				$v['update_time'] = '';
			}
		}
		$detail['comment_auth'] = 0;
		$type_user_array = explode(",", $detail['type_user']);
		if (in_array($this->uid, $type_user_array)) {
			$detail['comment_auth'] = 1;
		}
		$detail['comment']	= $comment;
        View::assign('detail', $detail);
        return view();
    }
	
	
    //删除汇报
    public function delete()
    {	
        $param = get_params();
        $id = $param['id'];
		$detail = Db::name('Work')->where(['id' => $id,'status' => 1])->find();
		if (empty($detail)) {
			echo '<div style="text-align:center;color:red;margin-top:20%;">该汇报不存在</div>';exit;
		}
		if($detail['admin_id'] !=$this->uid){
			$res = Db::name('WorkRecord')->where(['wid' => $detail['id'],'to_uid' => $this->uid,'status' => 1])->update(['status' => -1]);
		}
		else{
			Db::name('Work')->where('id',$detail['id'])->update(['status'=>-1]);
			$res = Db::name('WorkRecord')->where('wid',$detail['id'])->update(['status'=>-1]);
		}
		if($res!==false){
			add_log('delete', $param['id']);
			return to_assign();
		}
		else{
			return to_assign(1, '操作失败');
		}
	}
	
	
    //发送汇报
    public function send()
    {
        $param = get_params();
		if (!$param['uids']) {
			return to_assign(1, '接受人员不能为空');
		}
        //基础信息数据
        $admin_id = $this->uid;
        $basedata = [];
        $basedata['admin_id'] = $admin_id;
        $basedata['works'] = $param['works'];
        $basedata['type'] = $param['type'];
        $basedata['type_user'] = $param['uids'];
        $basedata['plans'] = $param['plans'];
        $basedata['remark'] = $param['remark'];
        $basedata['create_time'] = time();
		
		//新增信息
        $wid = Db::name('Work')->strict(false)->field(true)->insertGetId($basedata);
		if($wid!==false){
			//附件插入附件
            if (!empty($param['file_ids'])) {
                $file_array = explode(',', $param['file_ids']);
                $file_data = array();
                foreach ($file_array as $key => $value) {
                    if (!$value) {
                        continue;
                    }
                    $file_data[] = array(
                        'wid' => $wid,
                        'file_id' => $value,
                        'create_time' => time(),
                        'admin_id' => $admin_id,
                    );
                }
                if ($file_data) {
                    Db::name('WorkFileInterfix')->strict(false)->field(true)->insertAll($file_data);
                }
            }
			$users = explode(',',$param['uids']);
			//组合要发的消息
            $send_data = [];
            foreach ($users as $key => $value) {
                if (!$value || ($value == $admin_id)) {
                    continue;
                }
                $send_data[] = array(
					'wid' => $wid,//关联id
					'to_uid' => $value,//接收人
					'from_uid' => $admin_id,//发送人
					'send_time' => time()
                );
            }
			//组合要发的消息
			$types=['','日报','周报','月报','其他'];
			$msg=[
				'from_uid'=>$this->uid,
				'title' => $types[$param['type']],
				'action_id'=>$wid
			];
			sendMessage($users,5,$msg);
            $res = Db::name('WorkRecord')->strict(false)->field(true)->insertAll($send_data);
			add_log('send',$wid);
            return to_assign(0, '发送成功');
		}else {
            return to_assign(1, '发送失败');
        }
    }

}
