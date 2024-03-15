<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\contract\model;

use think\facade\Db;
use think\Model;

class Contract extends Model
{
	const ZERO = 0;
    const ONE = 1;
    const TWO = 2;
    const THREE = 3;
    const FORE = 4;
    const FIVE = 5;
    const SIX = 6;

    public static $Type = [
        self::ZERO => '未设置',
        self::ONE => '普通合同',
        self::TWO => '框架合同',
        self::THREE => '补充协议',
        self::FORE => '其他合同',
    ];
	
	public static $Status = [
        self::ZERO => '待审核',
        self::ONE => '审核中',
        self::TWO => '审核通过',
        self::THREE => '审核拒绝',
        self::FORE => '已撤销',
        self::FIVE => '已中止',
        self::SIX => '已作废',
    ];
	
	public static $ArchiveStatus = [
        self::ZERO => '未归档',
        self::ONE => '已归档',
    ];
	
	//列表检索
    public function get_list($param = [], $where = [], $whereOr=[])
    {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = self::where($where)
            ->where(function ($query) use($whereOr) {
					$query->whereOr($whereOr);
			})
			->field('a.*,a.type as type_a, c.title as cate_title,d.title as sign_department')
			->alias('a')
			->join('contract_cate c', 'a.cate_id = c.id')
			->join('department d', 'a.sign_did = d.id','LEFT')
			->order('a.create_time desc')
			->paginate($rows, false, ['query' => $param])
			->each(function ($item, $key) {
				$item->keeper_name = Db::name('Admin')->where(['id' => $item->keeper_uid])->value('name');
				$item->sign_name = Db::name('Admin')->where(['id' => $item->sign_uid])->value('name');
				$item->sign_time = date('Y-m-d', $item->sign_time);
				$item->interval_time = date('Y-m-d', $item->start_time) . ' 至 ' . date('Y-m-d', $item->end_time);
				$item->type_name = self::$Type[(int)$item->type_a];
				$item->status_name = self::$Status[(int)$item->check_status];
                $item->delay = countDays(date("Y-m-d"),date('Y-m-d', $item->end_time));
				if($item->cost == 0){
					$item->cost = '-';
				}
			});
        return $list;
    }
	
	
    // 获取合同详情
    public function detail($id)
    {
        $detail = self::where(['id' => $id])->find();
        if (!empty($detail)) {				
			$detail['status_name'] = self::$Status[(int) $detail['check_status']];	
			$detail['archive_status_name'] = self::$ArchiveStatus[(int) $detail['archive_status']];	
			$detail['sign_time'] = date('Y-m-d', $detail['sign_time']);
            $detail['start_time'] = date('Y-m-d', $detail['start_time']);
            $detail['end_time'] = date('Y-m-d', $detail['end_time']);
			$detail['cate_title'] = Db::name('ContractCate')->where(['id' => $detail['cate_id']])->value('title');
			$detail['sign_department'] = Db::name('Department')->where(['id' => $detail['sign_did']])->value('title');
			$detail['sign_name'] = Db::name('Admin')->where(['id' => $detail['sign_uid']])->value('name');
			$detail['admin_name'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
			$detail['prepared_name'] = Db::name('Admin')->where(['id' => $detail['prepared_uid']])->value('name');
			$detail['keeper_name'] = Db::name('Admin')->where(['id' => $detail['keeper_uid']])->value('name');
			
			$share_names = Db::name('Admin')->where([['id','in',$detail['share_ids']]])->column('name');
			$detail['share_names'] = implode(',',$share_names);
			
			//审核信息
			if($detail['check_uid'] > 0){
				$detail['check_name'] = Db::name('Admin')->where(['id' => $detail['check_uid']])->value('name');
				$detail['check_time'] = date('Y-m-d', $detail['check_time']);
			}
			//中止信息
			if($detail['stop_uid'] > 0){
				$detail['stop_name'] = Db::name('Admin')->where(['id' => $detail['stop_uid']])->value('name');
				$detail['stop_time'] = date('Y-m-d', $detail['stop_time']);
			}
			//作废信息
			if($detail['void_uid'] > 0){
				$detail['void_name'] = Db::name('Admin')->where(['id' => $detail['void_uid']])->value('name');
				$detail['void_time'] = date('Y-m-d', $detail['void_time']);
			}
			//归档信息
			if($detail['archive_status'] == 1){
				$detail['archive_name'] = Db::name('Admin')->where(['id' => $detail['archive_uid']])->value('name');
				$detail['archive_time'] = date('Y-m-d', $detail['archive_time']);
			}
			
			if($detail['pid']>0){
				$detail['pname'] = self::where(['id' => $detail['pid']])->value('name');
			}
			
			if($detail['file_ids'] !=''){
				$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['fileArray'] = $fileArray;
			}
        }
        return $detail;
    }
}
