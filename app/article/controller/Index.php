<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\article\controller;

use app\base\BaseController;
use app\article\model\Article as ArticleList;
use app\article\validate\ArticleCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$uid = $this->uid;
			$did = $this->did;
            $where = array();
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.title|a.keywords|a.desc|a.content|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['cate_id'])) {
                $where[] = ['a.cate_id', '=', $param['cate_id']];
            }
            $where[] = ['a.delete_time', '=', 0];
			
            $whereOr[] = ['a.is_share', '=', 1];			
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$did}',a.share_dids)")];
			$whereOr[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',a.share_uids)")];			
			
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $content = ArticleList::where($where)
				->where(function ($query) use($whereOr) {
					$query->whereOr($whereOr);
				})
                ->field('a.*,a.id as id,c.title as cate_title,a.title as title,d.title as department,u.name as user')
                ->alias('a')
                ->join('article_cate c', 'a.cate_id = c.id')
                ->join('admin u', 'a.uid = u.id','LEFT')
                ->join('department d', 'a.did = d.id','LEFT')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param]);
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }

    public function list()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.title|a.keywords|a.desc|a.content|c.title', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['cate_id'])) {
                $where[] = ['a.cate_id', '=', $param['cate_id']];
            }
            $where[] = ['a.delete_time', '=', 0];
            $where[] = ['a.uid', '=', $this->uid];
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $content = ArticleList::where($where)
                ->field('a.*,a.id as id,c.title as cate_title,a.title as title')
                ->alias('a')
                ->join('article_cate c', 'a.cate_id = c.id')
                ->order('a.create_time desc')
                ->paginate($rows, false, ['query' => $param]);
            return table_assign(0, '', $content);
        } else {
            return view();
        }
    }

    //文章添加&&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $DbRes = false;
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ArticleCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                Db::startTrans();
                try {
                    $res = ArticleList::strict(false)->field(true)->update($param);
                    $aid = $param['id'];
                    if ($res) {
                        //关联关键字
                        if (isset($param['keyword_names']) && $param['keyword_names']) {
                            Db::name('ArticleKeywords')->where(['aid' => $aid])->delete();
                            $keywordArray = explode(',', $param['keyword_names']);
                            $res_keyword = (new ArticleList())->insertKeyword($keywordArray, $aid);
                        } else {
                            $res_keyword = true;
                        }
                        if ($res_keyword !== false) {
                            add_log('edit', $param['id'], $param);
                            Db::commit();
                            $DbRes = true;
                        }
                    } else {
                        Db::rollback();
                    }
                } catch (\Exception $e) { ##这里参数不能删除($e：错误信息)
                Db::rollback();
                }
            } else {
                try {
                    validate(ArticleCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['uid'] = $this->uid;
                $param['did'] = $this->did;
                Db::startTrans();
                try {
                    if (empty($param['desc'])) {
                        $param['desc'] = get_desc_content($param['content'], 100);
                    }
                    $aid = ArticleList::strict(false)->field(true)->insertGetId($param);
                    if ($aid) {
                        //关联关键字
                        if (isset($param['keyword_names']) && $param['keyword_names']) {
                            $keywordArray = explode(',', $param['keyword_names']);
                            $res_keyword = (new ArticleList())->insertKeyword($keywordArray, $aid);
                        } else {
                            $res_keyword = true;
                        }
                        if ($res_keyword !== false) {
                            add_log('add', $aid, $param);
                            Db::commit();
                            $DbRes = true;
                        }
                    } else {
                        Db::rollback();
                    }
                } catch (\Exception $e) { ##这里参数不能删除($e：错误信息)
                Db::rollback();
                }
            }
            if ($DbRes) {
                return to_assign();
            } else {
                return to_assign(1, '操作失败');
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            View::assign('id', $id);
            if ($id > 0) {
                $article = (new ArticleList())->detail($id);
				if($article['file_ids'] !=''){
					$fileArray = Db::name('File')->where('id','in',$article['file_ids'])->select();
					$article['fileArray'] = $fileArray;
				}
				$article['share_depaments'] = '';
				if($article['share_dids'] !=''){
					$depamentArray = Db::name('Department')->where('id','in',$article['share_dids'])->column('title');
					$article['share_depaments'] = implode(',',$depamentArray);
				}
				$article['share_names'] = '';
				if($article['share_uids'] !=''){
					$uidArray = Db::name('Admin')->where('id','in',$article['share_uids'])->column('name');
					$article['share_names'] = implode(',',$uidArray);
				}
                View::assign('article', $article);
                return view('edit');
            }
            return view();
        }
    }

    //查看文章
    public function view()
    {
        $id = get_params("id");
		$uid=$this->uid;
		$did=$this->did;
        $detail = (new ArticleList())->detail($id);
		$share_uids = [];
		if(!empty($detail['share_uids'])){
			$share_uids = explode(',', $detail['share_uids']);
		}
		$share_dids = [];
		if(!empty($detail['share_dids'])){
			$share_dids = explode(',', $detail['share_dids']);
		}
		if($detail['uid'] !=$uid && !in_array($uid,$share_uids) && !in_array($did,$share_dids) && $detail['is_share'] !=1){
			throw new \think\exception\HttpException(405, '无权限访问');
		}
		$detail['cate_title'] = Db::name('ArticleCate')->where(['id' => $detail['cate_id']])->value('title');
		if($detail['file_ids'] !=''){
			$fileArray = Db::name('File')->where('id','in',$detail['file_ids'])->select();
			$detail['fileArray'] = $fileArray;
		}
		
		$comment = Db::name('ArticleComment')
			->field('a.*,u.name,u.thumb')
			->alias('a')
			->join('Admin u', 'u.id = a.admin_id')
			->order('a.create_time desc')
			->where(['a.article_id'=>$detail['id'],'a.delete_time' => 0])
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
		$detail['comment']	= $comment;
        // read 字段加 1
        Db::name('article')->where('id', $id)->inc('read')->update();
        View::assign('detail', $detail);
        return view();
    }
    //删除文章
    public function delete()
    {
        $id = get_params("id");
		$admin_id = Db::name('Article')->where('id',$id)->value('uid');
		if($admin_id!=$this->uid){
			return to_assign(1, "你不是该知识的创建人，没权限删除");
		}
        $data['id'] = $id;
        $data['delete_time'] = time();
        if (Db::name('Article')->update($data) !== false) {
            add_log('delete', $id);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }
}
