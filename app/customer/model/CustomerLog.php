<?php
/**
 * @copyright Copyright (c) 2022 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);
namespace app\customer\model;

use think\facade\Db;
use think\Model;

class CustomerLog extends Model
{
    public static $Sourse = [
		'status' => ['未设置', '新进客户', '跟进客户', '正式客户', '流失客户','已成交客户'],
		'intent_status' => ['未设置', '意向不明', '意向模糊', '意向一般', '意向强烈','已成交'],
		'type' => ['其他','电话','微信','QQ','上门'],
		'stage' => ['未设置','立项评估','初期沟通','需求分析','方案制定','商务谈判','合同签订','失单'],
		'action' => [			
			'add' => '创建',
			'edit' => '修改',
			'delete' => '删除',
			'upload' => '上传',
			'get' => '领取',
			'tosea' => '向公海放入',
			'totrash' => '向废池放入',
			'recovery' => '从废池移出',
		],
		'role'=>['客户','客户跟进记录','客户联系人','客户销售机会'],
		'field_array' =>[
			0 =>[
			'name' => '名称',
			'source_id' => '客户来源',
			'grade_id' => '客户等级',
			'industry_id' => '所属行业',
			'services_id' => '客户意向',
			'provinceid' => '省份',
			'cityid' => '城市',
			'distid'=> '区县',
			'address' => '联系地址',
			'status' => '状态',
			'intent_status' => '意向状态',
			'belong_uid' => '所属人',
			'belong_did' => '所属部门',
			'share_ids' => '共享人员',
			'content' => '客户描述',
			'market' => '主要经营业务',
			'remark' => '备注信息',
			'bank`' => '开户银行',
			'bank_sn' => '银行帐号',
			'tax_num' => '纳税人识别号',
			'cperson_mobile' => '开票电话',
			'cperson_address' => '开票地址',
			'discard_time' => '废弃时间',
			'delete_time' => '删除',
			'file' => '附件',
			'new' => '新增',
			'del' => '删除',
			'belong' => '所属人',
		],
		1 =>[
			'contact_id' => '联系人',
			'chance_id' => '销售机会',
			'type' => '跟进方式',
			'stage' => '当前阶段',
			'content' => '跟进内容',
			'follow_time' => '跟进时间',
			'next_time' => '下次跟进时间',
			'delete_time' => '删除',
			'new' => '新增',
			'del' => '删除',
		],
		2 =>[
			'name' => '姓名',
			'is_default' => '第一联系人',
			'sex' => '性别',
			'mobile' => '手机号码',
			'qq' => 'QQ号',
			'wechat' => '微信号',
			'email' => '邮件地址',
			'nickname' => '称谓',
			'department' => '部门',
			'position' => '职务',
			'delete_time' => '删除',
			'new' => '新增',
			'del' => '删除',
		],
		3 =>[
			'title' => '主题',
			'contact_id' => '联系人',
			'services_id' => '需求服务',
			'stage' => '当前阶段',
			'content' => '需求描述',
			'discovery_time' => '发现时间',
			'expected_time' => '预计签单时间',
			'expected_amount' => '预计签单金额',
			'belong_uid' => '所属人',
			'assist_ids' => '协助人员',
			'delete_time' => '删除',
			'new' => '新增',
			'del' => '删除',
		]
		]
    ];

    public function customer_log($param = [])
    {
		$trace_ids = Db::name('CustomerTrace')->where(['cid' => $param['customer_id'], 'delete_time' => 0])->column('id');
        $contact_ids = Db::name('CustomerContact')->where(['cid' => $param['customer_id'], 'delete_time' => 0])->column('id');
        $chance_ids = Db::name('CustomerChance')->where(['cid' => $param['customer_id'], 'delete_time' => 0])->column('id');
		
		$where1 = [];
        $where2 = [];
        $where3 = [];
        $where4 = [];

        $where1[] = ['a.customer_id', '=', $param['customer_id']];

        $where2[] = ['a.type', '=', 1];
        $where2[] = ['a.trace_id', 'in', $trace_ids];

        $where3[] = ['a.type', '=', 2];
        $where3[] = ['a.contact_id', 'in', $contact_ids];
		
		$where4[] = ['a.type', '=', 3];
        $where4[] = ['a.chance_id', 'in', $chance_ids];
		
        $page = intval($param['page']);
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $content = Db::name('CustomerLog')
            ->field('a.*,u.name,u.thumb')
            ->alias('a')
            ->join('Admin u', 'u.id = a.admin_id')
            ->order('a.create_time desc')
            ->whereOr([$where1, $where2, $where3, $where4])
            ->page($page, $rows)
            ->select()->toArray();
        $data = [];
		$sourse = self::$Sourse;
		$role = $sourse['role'];
		$action = $sourse['action'];
        foreach ($content as $k => $v) {
			$field_array = $sourse['field_array'][$v['type']];
			if (isset($sourse[$v['field']])) {
                $v['old_content'] = $sourse[$v['field']][$v['old_content']];
                $v['new_content'] = $sourse[$v['field']][$v['new_content']];
            }			
            if (strpos($v['field'], '_time') !== false) {
                if ($v['old_content'] == '') {
                    $v['old_content'] = '未设置';
                }
                $v['new_content'] = date('Y-m-d', (int) $v['new_content']);
            }
            if (strpos($v['field'], '_uid') !== false) {
                $v['old_content'] = Db::name('Admin')->where(['id' => $v['old_content']])->value('name');
                $v['new_content'] = Db::name('Admin')->where(['id' => $v['new_content']])->value('name');
            }
            if ($v['field'] == 'contact_id') {
                $v['old_content'] = Db::name('CustomerContact')->where(['id' => $v['old_content']])->value('name');
                $v['new_content'] = Db::name('CustomerContact')->where(['id' => $v['new_content']])->value('name');
            }
			if ($v['field'] == 'source_id') {
                $v['old_content'] = Db::name('CustomerSource')->where(['id' => $v['old_content']])->value('title');
                $v['new_content'] = Db::name('CustomerSource')->where(['id' => $v['new_content']])->value('title');
            }
			if ($v['field'] == 'grade_id') {
                $v['old_content'] = Db::name('CustomerGrade')->where(['id' => $v['old_content']])->value('title');
                $v['new_content'] = Db::name('CustomerGrade')->where(['id' => $v['new_content']])->value('title');
            }
			if ($v['field'] == 'industry_id') {
                $v['old_content'] = Db::name('Industry')->where(['id' => $v['old_content']])->value('title');
                $v['new_content'] = Db::name('Industry')->where(['id' => $v['new_content']])->value('title');
            }
			if ($v['field'] == 'services_id') {
                $v['old_content'] = Db::name('Services')->where(['id' => $v['old_content']])->value('title');
                $v['new_content'] = Db::name('Services')->where(['id' => $v['new_content']])->value('title');
            }
			if ($v['field'] == 'is_default') {
                $v['old_content'] = $v['old_content'] == 1?'第一联系人':'普通联系人';
                $v['new_content'] = $v['new_content'] == 1?'第一联系人':'普通联系人';
            }
			if ($v['field'] == 'sex') {
                $v['old_content'] = $v['old_content'] == 1?'男':'女';
                $v['new_content'] = $v['new_content'] == 1?'男':'女';
            }
            if (strpos($v['field'], '_ids') !== false) {
                $old_ids = Db::name('Admin')->where('id', 'in', $v['old_content'])->column('name');
                $v['old_content'] = implode(',', $old_ids);
                $new_ids = Db::name('Admin')->where('id', 'in', $v['new_content'])->column('name');
                $v['new_content'] = implode(',', $new_ids);
            }
            if ($v['old_content'] == '' || $v['old_content'] == null) {
                $v['old_content'] = '未设置';
            }
            if ($v['new_content'] == '' || $v['new_content'] == null) {
                $v['new_content'] = '未设置';
            }
            $v['role'] = $role[$v['type']];
            $v['action'] = $action[$v['action']];
            $v['title'] = $field_array[$v['field']];
            $v['times'] = time_trans($v['create_time']);
            $v['create_time'] = date('Y-m-d', $v['create_time']);
            $data[] = $v;
        }
        return $data;
    }
}
