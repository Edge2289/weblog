<?php
namespace app\api\controller;

use think\Db;
use app\common\model\CateModel;
use app\common\model\ArticleModel;
use app\api\common\Base;

/**
*  前台接口
*/
class Index extends Base
{
	
	function __construct()
	{
		// parent::__construct();
		header('Content-Type: text/html;charset=utf-8');
	    header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
	    header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
	    header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
	    header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
	}


	/**
	 * [is_login 验证登录]
	 * @return boolean [description]
	 */
	public function is_login(){

		$param = $this->request->param();
	}

	/**
	 * [cateData 获取分类]
	 * @return [type] [description]
	 */
	public function cateData(){

		$data = CateModel::where('is_state', 1)->order('cate_sort desc')->field('cate_id,cate_name')->select();
		return DataReturn(1, '请求成功', $data);
	}


	public function articleData(){
		$type = input('get.type');
		$data = ArticleModel::articleSel($type);
		return DataReturn(1, '请求成功', $data);
	}

	public function articleHtml(){
		$article_id = input('get.arti');
		$data['html'] = ArticleModel::where('article_id',$article_id)
					->where('article_is_del',2)
					->where('article_is_state',1)
					->field('article_title,article_nick,article_text,read_sum,click_sum,is_comment')
					->find()
					->toArray();
		$comment = Db::query('select a.*,b.user_nick from blog_comment a left join blog_user b on a.user_id = b.user_id where a.article_id = '.$article_id.' order by comment_id desc');
		$data['comment'] = commentTree($comment) ;
		// dd($data['comment'][1]['children']);
		dd(commentBig($data['comment']));
		return DataReturn(1, '请求成功', $data);
	}

	public function artHotNew(){
		$data['newd'] = ArticleModel::where('article_is_del',2)
									->where('article_is_state', 1)
									->order('article_time desc')
									->field('article_title,article_nick,article_text,read_sum,article_img,click_sum,is_comment')
									->limit(6)
									->select()
									->toArray();

		$data['hot'] = ArticleModel::where('article_is_del',2)
									->where('article_is_state', 1)
									->field('article_title,article_nick,article_text,article_img,read_sum,click_sum,is_comment')
									->order('article_hot desc')
									->limit(6)
									->select()
									->toArray();
		return DataReturn(1, '请求成功', $data);
	}
}