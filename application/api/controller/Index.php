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
					->field('article_id,article_title,article_nick,article_text,read_sum,click_sum,is_comment')
					->find()
					->toArray();
		// $comment = Db::query('SELECT a.*,b.user_nick as target_nick,c.user_nick,c.user_img FROM blog_comment a left JOIN blog_user b ON a.target_id = b.user_id left JOIN blog_user c ON a.user_id = c.user_id where a.article_id = '.$article_id.' order by comment_id desc');
		$comment = Db::query('SELECT a.*,b.user_id AS target_user_id,c.user_nick,c.user_img FROM blog_comment a LEFT JOIN blog_comment b ON a.target_id = b.comment_id left JOIN blog_user c ON a.user_id = c.user_id where a.article_id = '.$article_id.' order by comment_id desc');
		$data['comment'] = commentTree($comment);
		$data['comment'] = commentBig($data['comment']);
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


    // 登录是否失效
    public function isLogin(){
        $param = Request()->param();
        $i = Db('blog_user')->where($param)->field('user_id')->find();
        if ($i) {
           return DataReturn(1,'有效',[]);
        }else{
            return DataReturn(-1, '请重新登录', []);
        }
    }

    /**
     * [commentIns 添加评论]
     * @return [type] [description]
     */
    public function commentIns(){
    	if (!Request()->isPost()) {
    		return DataReturn(-1,'错误提交',[]);
    	}
    	$param = Request()->param();
    	if (empty($param['sqq']) || strlen($param['sqq']) != 32) {
    		return DataReturn(-1,'错误提交',[]);
    	}
    	$userI = Db('blog_user')->where('user_qq',$param['sqq'])->value('user_id');
    	if (!$userI) {
    		return DataReturn(-1,'请重新登录',[]);
    	}
    	$map['target_id'] = 0;
    	if (!empty($param['tid'])) {
    		$map['target_id'] = $param['tid'];
    	}
    	$map['comment_val'] = $param['oSize'];
    	$map['user_id'] = $userI;
    	$map['article_id'] = $param['aid'];
    	$map['comment_time'] = time();
    	$it = Db('blog_comment')->insert($map);
    	if ($it) {
    		return DataReturn(1,'评论成功',[]);
    	}else{
    		return DataReturn(0, '评论失败',[]);
    	}
    }

    /**
     * [commentDel 删除评论]
     * @return [type] [description]
     */
    public function commentDel(){

    }
}