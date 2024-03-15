<?php
namespace app\finance\model;
use think\Model;
use think\facade\Db;
class Invoice extends Model
{
	//发票列表检索
	public function get_list($param=[],$where = [], $type='and')
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		if($type == 'or'){
			$list = Invoice::field('i.*,a.name,d.title as department_name')
				->alias('i')
				->join('Admin a', 'a.id = i.admin_id', 'left')
				->join('Department d', 'd.id = i.did', 'left')
				->whereOr($where)
				->order('i.id desc')
				->group('i.id')
				->paginate(['list_rows' => $rows, 'query' => $param])
				->each(function($item, $key){
					if ($item['open_time'] > 0) {
						$item['open_time'] = empty($item['open_time']) ? '0' : date('Y-m-d', (int)$item['open_time']);
						$item['open_name'] = Db::name('Admin')->where('id',$item['open_admin_id'])->value('name');
					}
					else{
						$item['open_time'] = '';
						$item['open_name'] = '-';
					}
					$item['check_user'] = '-';
					if($item['check_status']==1 && !empty($item['check_admin_ids'])){
						$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',',$check_user);
					}
					return $item;
				});
		}
		else{
			$list = Invoice::field('i.*,a.name,d.title as department_name')
				->alias('i')
				->join('Admin a', 'a.id = i.admin_id', 'left')
				->join('Department d', 'd.id = i.did', 'left')
				->where($where)
				->order('i.id desc')
				->paginate(['list_rows' => $rows, 'query' => $param])
				->each(function($item, $key){
					$item['check_user'] = '-';
					if ($item['open_time'] > 0) {
						$item['open_time'] = empty($item['open_time']) ? '0' : date('Y-m-d', (int)$item['open_time']);
						$item['open_name'] = Db::name('Admin')->where('id',$item['open_admin_id'])->value('name');
					}
					else{
						$item['open_time'] = '';
						$item['open_name'] = '-';
					}
					if($item['check_status'] == 1 && !empty($item['check_admin_ids'])){
						$check_user = Db::name('Admin')->where('id','in',$item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',',$check_user);
					}
					return $item;
				});
		}
        return $list;
    }
	
	//到账列表检索
    public function income_list($param = [], $where = [])
    {
        $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
        $list = Invoice::where($where)
            ->order('is_cash asc,create_time desc')
            ->paginate($rows, false, ['query' => $param])
            ->each(function ($item, $key) {
                $item->user = Db::name('Admin')->where(['id' => $item->admin_id])->value('name');
                $item->department = Db::name('Department')->where(['id' => $item->did])->value('title');
                $item->enter_time = empty($item->enter_time) ? '-' : date('Y-m-d H:i', $item->enter_time);
                $item->open_name = Db::name('Admin')->where(['id' => $item->open_admin_id])->value('name');
                $item->open_time = empty($item->open_time) ? '-' : date('Y-m-d H:i', $item->open_time);
            });
        return $list;
    }
	
    public function detail($id = 0)
    {
        $detail = Invoice::where(['id' => $id])->find();
        if ($detail) {
			$detail['create_user'] = Db::name('Admin')->where(['id' => $detail['admin_id']])->value('name');
            $detail['department'] = Db::name('Department')->where(['id' => $detail['did']])->value('title');
            if ($detail['open_time'] > 0) {
                $detail['open_time'] = empty($detail['open_time']) ? '0' : date('Y-m-d', $detail['open_time']);
				$detail['open_admin'] = Db::name('Admin')->where(['id' => $detail['open_admin_id']])->value('name');
            }
            else{
                $detail['open_time'] = '-';
                $detail['open_admin'] = '-';
            }
			if ($detail['contract_id'] > 0) {
				$detail['contract_name'] = Db::name('Contract')->where(['id' => $detail['contract_id']])->value('name');
            }
            else{
                $detail['contract_name'] = '';
            }			
        }
        return $detail;
    }
	
	
}