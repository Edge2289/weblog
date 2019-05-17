<?php
namespace app\api\controller;

use think\Db;
use think\Exception;
use think\Request;
use app\common\model\UserModel;
use app\common\model\ChatGroupMember;
use app\common\model\ChatMyGroupModel;


/**
*  name 小小的成
*  聊天api 类
*/
class Chat
{
	private $opend;
	private $token;
	private $fd;
	protected $request;

	public function __construct()
	{
		// parent::__construct();
		header('Content-Type: text/html;charset=utf-8');
	    header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
	    header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
	    header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
	    header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
		$this->request = Request::instance();
		// $this->isLogin($this->request->param());
	}

	public function isLogin($param){
		if (empty($param['token']) || empty($param['opend'])) {
			echo $this->reData(0, '请重新登录',['url' => '登录地址']);
			die;
		}
		$i = Db('blog_user')
			->where('user_qq',$param['opend'])
			->where('access_token',$param['token'])
			->find();
		if (!$i) {
			echo $this->reData(0, '请重新登录-',['url' => '登录地址']);
			die;
		}
	}

	/**
	 * [userInit 登录初始化]
	 * @return [type] [description]
	 */
	public function userInit(){
		$param = $this->request->param();

		try {
			// 查询自己的信息
			$userData = UserModel::where('user_qq',$param['opend'])->find()->toArray();
			// 先查询分组 后查询分组里面的朋友
			$groupData = ChatMyGroupModel::groupfriend($param['opend']);
			// 查询加入了什么群
			$groupmemberData = ChatGroupMember::group($param['opend']);
			UserModel::where('user_qq',$param['opend'])->update(['is_chat_status' => 1]);
		} catch (Exception $e) {
			return $this->reData(0, $e->getMessage(),[]);
		}
		return [
              "code" => 0 
              ,"msg" => "" 
              ,"data" => [
              
                "mine" =>  [
                  "username" =>  $userData["user_nick"]
                  ,"id" =>  $userData["user_qq"] 
                  ,"status" =>  $userData["is_chat_status"] 
                  ,"sign" =>  $userData["user_nick"]."说: 热爱PHP！"
                  ,"avatar" => $userData["user_img"]
                ]
                
                ,"friend" =>  $groupData
                
                ,"group" =>  $groupmemberData
              ]
            ];
	}


	public function ceshi(){
		dd(ChatMyGroupModel::friendList('2CB99992FE060C4B897B0E9419887AC8'));
	}

	/**
	 *  上传图片
	 *      {
	      "code": 0 //0表示成功，其它表示失败
	      ,"msg": "" //失败信息
	      ,"data": {
	        "src": "http://cdn.xxx.com/upload/images/a.jpg" //图片url
	      }
    }      
          
	 */

	public function chatImg(){
		if (!$this->request->isPost()) {
			$this->reData(0,404,[]);die;
		}
		$file = request()->file();
		if (!$file) {
			$this->reData(0,404,[]);die;
		}

		$roth = ROOT_PATH . 'public' . DS . 'static'. DS . 'img'. DS . 'chat';
		$info = $file["file"]->move($roth);
		if($info){
            // 成功上传后 获取上传信息
            $src = '\public' . DS . 'static'. DS . 'img'. DS . 'chat'. DS .date("Ymd"). DS .$info->getFilename();
            return [
            			'code' => 0,
            			'msg'  => 'success',
            			'data'  => [
            				'src'  => $src,
            			],
            	]; 
        }else{
            // 上传失败获取错误信息
            return [
            			'code'  => 1,
            			'msg'  => $file["file"]->getError(),
            			'data'  => []
            	];
        }
	}

	/**
	 *  上传文件
	 *  {
		  "code": 0 //0表示成功，其它表示失败
		  ,"msg": "" //失败信息
		  ,"data": {
		    "src": "http://cdn.xxx.com/upload/file/LayIM.zip" //文件url
		    ,"name": "LayIM.zip" //文件名
		  }
		}      
	 */

	public function chatFile(){

	}



	/**
	 *  查看群成员
	 *  {
  "code": 0 //0表示成功，其它表示失败
  ,"msg": "" //失败信息
  ,"data": {
    "list": [{
      "username": "马小云" //群员昵称
      ,"id": "168168" //群员id
      ,"avatar": "http://tp4.sinaimg.cn/2145291155/180/5601307179/1" //群员头像
      ,"sign": "让天下没有难写的代码" //群员签名
    }, …… ]
  }
}      
	 */

	public function getMembers(){
		$data = $this->request->param();
		// group_id
		$groupData = ChatGroupMember::groupData($data['id']);
		$gData = [];
		foreach ($groupData as $gdk => $gdv) {
			$gData[$gdk]['username'] = $gdv['nickName'];
			$gData[$gdk]['id'] = $gdv['groupMemberIdx'];
			$gData[$gdk]['sign'] = "PHP 是世界上最好的语言";
			$gData[$gdk]['avatar'] = $gdv['user_img']['user_img'];
		}
		return [
            		'code'  => 0,
            		'msg'  => '',
            		'data'  => [
            			'list' => $gData
            		],
            	];
	}

	/**
	 * [reData 返回数据]
	 * @param  integer $code [状态]
	 * @param  string  $msg  [信息]
	 * @param  [type]  $data [数据]
	 * @return [type]        [json]
	 */
	public function reData($code = 1, $msg = '', $data 	= []){

		return json_encode([
				'code' => $code,
				'msg' => $msg,
				'data' => $data,
			]);
	}
}