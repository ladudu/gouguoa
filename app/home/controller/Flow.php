<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use app\home\validate\FlowCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Flow extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
			$type = get_config('approve.type');
            $list = Db::name('Flow')
				->field('f.*,a.name as username,t.title as flow_cate')
				->alias('f')
                ->join('Admin a', 'a.id = f.admin_id', 'left')
                ->join('FlowType t', 't.id = f.flow_cate', 'left')
                ->select()->toArray();
			foreach ($list as $key => &$value){
				$department = Db::name('Department')->where('id','in',$value['department_ids'])->column('title');
				$value['department'] = implode(',',$department);
				if($value['department']==''){
					$value['department'] = '全公司';
				}
				foreach ($type as $k => $val){
					if($value['type'] == $val['id']){
						$value['type_name'] = $val['title'];						
					}
				}
			}
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加新增/编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$param['flow_list'] = '';
			$flow_list=[];			
			if($param['check_type']==1){
				$flowTypeData = isset($param['flowType']) ? $param['flowType'] : '';
				$flowUidsData = isset($param['flowUidsA']) ? $param['flowUidsA'] : '';
				foreach ($flowTypeData as $key => $value) {
					if (!$value) {
						continue;
					}
					if($value>2 && $flowUidsData[$key]==''){
						return to_assign(1, '第'.($key+1).'行的指定人未选择');
						break;
					}
					$item = [];
					$item['flow_type'] = $value;
					$item['flow_uids'] = $flowUidsData[$key];
					$flow_list[]=$item;	
				}
				$param['flow_list'] = serialize($flow_list);
			}
			if($param['check_type']==3){
				$flowNameData = isset($param['flowName']) ? $param['flowName'] : '';
				$flowUidsData = isset($param['flowUidsB']) ? $param['flowUidsB'] : '';
				foreach ($flowNameData as $key => $value) {
					if (!$value) {
						continue;
					}
					if($flowUidsData[$key]==''){
						return to_assign(1, '第'.($key+1).'行的指定人未选择');
						break;
					}
					$item = [];
					$item['flow_name'] = $value;
					$item['flow_type'] = 5;
					$item['flow_uids'] = $flowUidsData[$key];
					$flow_list[]=$item;	
				}
				if(empty($flow_list)){
					return to_assign(1, '审批流程信息未完善');
				}
				$param['flow_list'] = serialize($flow_list);
			}		
            if ($param['id'] > 0) {
                try {
                    validate(FlowCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                Db::name('Flow')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                try {
                    validate(FlowCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['admin_id'] = $this->uid;
                $param['create_time'] = time();
                $mid = Db::name('Flow')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $mid, $param);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if($id>0){
                $detail = Db::name('Flow')->where('id',$id)->find();
                $flow_list = unserialize($detail['flow_list']);
				if(!empty($flow_list)){
					foreach ($flow_list as $key => &$val) {
						$val['flow_unames'] ='';
						if($val['flow_type']>2){
							$flow_unames = Db::name('Admin')->where('id', 'in', $val['flow_uids'])->column('name');
							$val['flow_unames'] = implode(',', $flow_unames);
						}
					}
				}
				$detail['flow_list'] = $flow_list;
				$detail['copy_unames'] ='';
				if($detail['copy_uids']!=''){
					$copy_unames = Db::name('Admin')->where('id', 'in', $detail['copy_uids'])->column('name');
					$detail['copy_unames'] = implode(',', $copy_unames);
				}
                $detail['flow_cate_list'] = Db::name('FlowType')->where(['type'=>$detail['type'],'status'=>1])->select()->toArray();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            View::assign('type', get_config('approve.type'));
            return view();
        }
    }

    //添加&编辑
    public function flow()
    {
        $param = get_params();
        if (request()->isAjax()) {
			
		}
		else{
			return view();
		}
    }

    //禁用/启用
    public function check()
    {
        $param = get_params();
		$param['update_time']= time();
		$res = Db::name('Flow')->strict(false)->field('status,update_time')->update($param);
		if($res!==false){
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
}
