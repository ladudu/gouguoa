<?php
namespace app\finance\model;
use think\Model;
use think\facade\Db;
class Expense extends Model
{
	public function get_list($param = [],$where = [], $type='and')
    {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		if($type == 'or'){
			$expense = Expense::whereOr($where)
				->order('id desc')
				->paginate($rows, false, ['query' => $param])
				->each(function ($item, $key) {
					$item->income_month = empty($item->income_month) ? '-' : date('Y-m', $item->income_month);
					$item->expense_time = empty($item->expense_time) ? '-' : date('Y-m-d', $item->expense_time);
					$item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
					$item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
					$item->pay_name = Db::name('Admin')->where(['id' => $item->pay_admin_id])->value('name');
					$item->pay_time = empty($item->pay_time) ? '-' : date('Y-m-d H:i', $item->pay_time);
					$item->amount = Db::name('ExpenseInterfix')->where(['exid' => $item->id])->sum('amount');
					$item['check_user'] = '-';
					if($item['check_status']==1 && !empty($item['check_admin_ids'])){
						$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',',$check_user);
					}
				});
		}
		else{
        $expense = Expense::where($where)
            ->order('id desc')
            ->paginate($rows, false, ['query' => $param])
            ->each(function ($item, $key) {
                $item->income_month = empty($item->income_month) ? '-' : date('Y-m', $item->income_month);
                $item->expense_time = empty($item->expense_time) ? '-' : date('Y-m-d', $item->expense_time);
                $item->admin_name = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
                $item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
                $item->pay_name = Db::name('Admin')->where(['id' => $item->pay_admin_id])->value('name');
                $item->pay_time = empty($item->pay_time) ? '-' : date('Y-m-d H:i', $item->pay_time);
                $item->amount = Db::name('ExpenseInterfix')->where(['exid' => $item->id])->sum('amount');
				$item['check_user'] = '-';
				if($item['check_status']==1 && !empty($item['check_admin_ids'])){
					$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
					$item['check_user'] = implode(',',$check_user);
				}
            });
		}
        return $expense;
    }

    public function detail($id = 0)
    {
        $expense = Expense::where(['id' => $id])->find();
        if ($expense) {
            $expense['income_month'] = empty($expense['income_month']) ? '-' : date('Y-m', $expense['income_month']);
            $expense['expense_time'] = empty($expense['expense_time']) ? '-' : date('Y-m-d', $expense['expense_time']);
            $expense['create_user'] = Db::name('Admin')->where(['id' => $expense['admin_id']])->value('name');
            $expense['department'] = Db::name('Department')->where(['id' => $expense['did']])->value('title');
            $expense['amount'] = Db::name('ExpenseInterfix')->where(['exid' => $expense['id']])->sum('amount');
            if ($expense['pay_time'] > 0) {
                $expense['pay_time'] = date('Y-m-d H:i:s', $expense['pay_time']);
				$expense['pay_admin'] = Db::name('Admin')->where(['id' => $expense['pay_admin_id']])->value('name');
            }
            else{
                $expense['pay_time'] = '-';
            }
			if ($expense['ptid'] > 0) {
                $expense['ptname'] = Db::name('Project')->where(['id' => $expense['ptid']])->value('name');
            }
            else{
                $expense['ptname'] = '';
            }
            $expense['list'] = Db::name('ExpenseInterfix')
                ->field('a.*,c.title as cate_title')
                ->alias('a')
                ->join('ExpenseCate c', 'a.cate_id = c.id','LEFT')
                ->where(['a.exid' => $expense['id']])
                ->select();
        }
        return $expense;
    }
}