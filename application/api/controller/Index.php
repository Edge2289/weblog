<?php
namespace app\api\controller;

use think\Db;
use think\Request;
use app\api\common\Base;
use app\common\model\CateModel;
use app\common\model\ArticleModel;

/**
*  前台接口
*/
class Index extends Base
{
	
	function __construct()
	{
	
		$request = Request::instance();
		// 判断域名
		if(!strpos($request->domain(),Config('url'))){
			$this->redirect("http://www.uikiss.cn");
		}
	
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

    /**
     * [articleHtml 文章的显示]
     * @return [type] [description]
     */
	public function articleHtml(){
		$article_id = input('get.arti');
		if(empty($article_id)){
			return DataReturn(0, '暂无文章', '');die;
		}
        // 文章信息
		$data['html'] = ArticleModel::where('article_id',$article_id)
					->where('article_is_del',2)
					->where('article_is_state',1)
					->field('article_id,article_title,article_nick,article_text,read_sum,click_sum,is_comment,article_markdown')
					->find()
					->toArray();
					
        $data['upper'] = ArticleModel::where('article_id','<',$article_id)
                                        ->where('article_is_del',2)
                                        ->where('article_is_state',1)
                                        ->field('article_id,article_title')
                                        ->order('article_id desc')
                                        ->limit(1)
                                        ->find();
        $data['down'] = ArticleModel::where('article_id','>',$article_id)
                                        ->where('article_is_del',2)
                                        ->where('article_is_state',1)
                                        ->field('article_id,article_title')
                                        ->limit(1)
                                        ->find();
        $data['upper'] = empty($data['upper']) ? ['article_id'=>'','article_title'=>'到顶了'] : $data['upper']->toArray();
        $data['down'] = empty($data['down']) ? ['article_id'=>'','article_title'=>'到底啦'] : $data['down']->toArray();
		$comment = Db::query('SELECT a.*,b.user_id AS target_user_id,c.user_nick,c.user_img FROM blog_comment a LEFT JOIN blog_comment b ON a.target_id = b.comment_id left JOIN blog_user c ON a.user_id = c.user_id where a.article_id = '.$article_id.' order by comment_id desc');
		$data['comment'] = commentTree($comment);
		$data['comment'] = commentBig($data['comment']);
		return DataReturn(1, '请求成功', $data);
	}

	public function artindex(){

		// 最新
		$data['newd'] = ArticleModel::cateapi();
		// 热门
		$data['hot'] = ArticleModel::cateapi('article_hot');
		// 导航图
    	$data['banner'] = Db("blog_banner")
    				->where('is_state',1)
    				->field('banner_title,banner_id,banner_url,color,banner_img')
    				->order('banner_time desc')
    				->select();
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
    	$map['comment_val'] = htmlentities($param['oSize']);
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
    	return DataReturn('-1','暂不支持删除评论',[]);
    }

    /**
     * [syslist 系统参数]
     * @return [type] [description]
     */
    public function syslist(){

    	// 前台参数
    	$arr = ['homeCopyright','honeTitle'];
    	$sys = Db('blog_sys')->select();
    	$data = [];
    	foreach ($sys as $key => $value) {
    		if (in_array($value['name'],$arr)) {
    			$data[] = $sys[$key];
    		}
    	}
    		return DataReturn(1, '请求成功',$data);
    }


    /**
     * [userlogin 登录日志]
     * @return [type] [description]
     */
    public function userlogin(){

        $map['user_qq'] = input('get.user_qq');
        $map['ip'] = getIPInfo();
        $reload = getCity($map['ip']);
        $map['region'] = $reload['data']['region'];
        $map['city'] = $reload['data']['city'];
        $info = explode (" ",$reload["info"]);
        $map['phone'] = getOS();//$info[count($info)-1];
        $map['safari'] = $info[count($info)-2];
        Db('blog_userlogin_log')->insert($map); // 进行数据插入操作
    }

    /**
     * [sourceget 访问数据]
     * @return [type] [description]
     */
    public function sourceget(){
        $param = Request()->param();
        if (empty($param['localhref'])) {
            return ['参数错误'];
            die;
        }
        /**
         * [$refere 返回]
         * @var  keyWord [关键词]
         * @var  fromtype [过来的平台]
         * @var [type]
         */
        $refere = getSourceCli($param['referer']);
        $map['source_url'] =  $param['localhref'];   //  来源
        $map['source_keywork'] =  $refere['keyWord'];
        $map['source_referer'] =  $refere['fromtype'] == "手动打开" ? "manually" : $refere['fromtype'];
        $map['source_ip'] =  getIPInfo();  // ip
        $m = getCity($map['source_ip']);
        $map['source_city'] =  $m['data']['city'];        // 城市
        $map['source_region'] =  $m['data']['region'];   // 省份 

        $info = explode (" ",$m["info"]);
        $map['source_phone'] =  getOS();   // 操作系统 
        $map['source_safari'] =  $info[count($info)-2];  // 浏览器类型 
        $map['source_time'] =  time();   // 时间 
        Db('blog_source')->insert($map);
    }

    /**
     * [uvcollect uv 收集api记录]
     * @return [type] [description]
     */
    public function uvcollect(){
        /**
         * id  
         * cookie
         * time 
         */
        if (!Request()->isGet()) {
            return ['很棒'];
        }
        $map['cookie'] = input('get.cookie');
        $map['time'] = time();
        $map['ip'] = getIPInfo();
        Db('blog_uv')->insert($map);
    }


    public function getMembers(){
        return [
              "code" =>  0 //0表示成功，其它表示失败
              ,"msg" =>  "" //失败信息
              ,"data" =>  [
                "list" =>  [[
                  "username" =>  "马小云" //群员昵称
                  ,"id" =>  "168168" //群员id
                  ,"avatar" =>  "http => //tp4.sinaimg.cn/2145291155/180/5601307179/1" //群员头像
                  ,"sign" =>  "让天下没有难写的代码" //群员签名
                ]]
              ]
            ]    ;  
    }

    public function _empty(){
    	return ['很棒'];
    }
}