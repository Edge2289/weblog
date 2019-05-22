<?php
namespace app\api\controller;

use think\Db;
use think\Exception;
use think\Request;
use app\common\model\UserModel;
use app\common\model\ChatRequest;
use app\common\model\ChatGroupModel;
use app\common\model\ChatGroupMember;
use app\common\model\ChatMyGroupModel;
use app\common\model\ChatMyFriendModel;


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
		return 1;
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
			// 别人请求我的
			$msgbox = Db('blog_chat_request')->where([
						'to_id' => $param['opend'],
						'status' => 0
				])->count();
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
                  ,"msgbox" => $msgbox
                ]
                
                ,"friend" =>  $groupData
                
                ,"group" =>  $groupmemberData
              ]
            ];
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
	 * [addfriend 添加好友]
	 * @return [type] [description]
	 */
	public function addfriend(){
		// 获取参数
		$data = $this->request->param();
		// 判断登录是否过期
		if (!$this->isLogin($data)) {
			return 0;die;
		};

		if ($data['type'] == 1) {


			// 数据操作 判断是否有记录
			// 当记录的状态返回 1 的时候  已做处理
			$reData = Db('blog_chat_request')->where([
					're_id' => $data['requestId'],
					'to_id' => $data['opend'],
					'status' => 0,
				])->find();
			if (!$reData) {
				return $this->reData(0, '没有此请求', []);
			}
			// 做判断
			$userFriend = Db::query('SELECT * from blog_chat_my_group a inner JOIN blog_chat_my_friend b ON a.mygroupIdx = b.mygroupIdx where a.opend = "'.$data['opend'].'" and b.opend = "'.$reData['form_id'].'"');
			if ($userFriend) {
				return $this->reData(0,'该用户已经是你的好友',[]);
			}
			# 这个是同意
			// 当未添加好友时 添加好友
			// 1 先同意者添加
			try{
				$toData = UserModel::where('user_qq',$reData['form_id'])->field('user_nick,is_chat_sign,user_img')->find();
				// $to_map['mygroupIdx'] = $data['group'];
				// $to_map['opend'] = $reData['form_id'];
				// $to_map['nickName'] = $toData['user_nick'];

				// $form_map['mygroupIdx'] = $reData['group_id'];
				// $form_map['opend'] = $reData['to_id'];
				// $form_map['nickName'] = UserModel::where('user_qq',$reData['to_id'])->value('user_nick');
				// $f_i = ChatMyFriendModel::insert($form_map);
				// $t_i = ChatMyFriendModel::insert($to_map);
				// $c_s = ChatRequest::where('re_id',$data['requestId'])->update(['to_status'=>1,'status'=>1,'to_read'=>0,'o_time'=>time()]);
				// if (!($f_i && $t_i && $c_s)) {
				// 	throw new Exception("添加好友失败");
				// }

				// 返回好友信息  用于追加上面板
				$reUserData['type'] = 'friend';
				$reUserData['avatar'] = $toData['user_img'];
				$reUserData['username'] = $toData['user_nick'];
				$reUserData['groupid'] = $data['group'];
				$reUserData['id'] = $reData['form_id'];
				$reUserData['sign'] = $toData['is_chat_sign'];
				$reUserData['type'] = 1;
				$reUserData['re_id'] = $data['requestId'];

				return $this->reData(1,"添加成功", $reUserData);
				// 后 申请者添加
			} catch(Exception $e){
				return $this->reData(0,$e->getMessage(), []);
			}
		}else{
			// 群
			try{
				$reData = Db('blog_chat_request')->where([
						're_id' => $data['requestId'],
						'group_id' => $data['opend'],
						'status' => 0,
					])->field('to_id,form_id')->find();
				if (!$reData) {
					return $this->reData(0, '没有此请求', []);
				}
				// 判断是否在群里面
				$igu = ChatGroupMember::where([
							'groupIdx' => $reData['to_id'],
							'opend' => $data['opend'],
							])->count();
				if ($igu) {
					return $this->reData(0,'已是群成员',[]);
				}

				$dataGroup = ChatGroupModel::where('groupIdx',$reData['to_id'])->field('number,approval')->find();
				if (!$dataGroup) {
					return $this->reData(0,'该群已解散',[]);
				}
				$userCount = ChatGroupMember::where('groupIdx',$reData['to_id'])->count();
				$dataGroup = $dataGroup->toArray();
				if ($userCount >= $dataGroup['number']) {
					return $this->reData(0,'该群已满人',[]);
				}
				// 添加进群操作
				
					$cmfData['groupIdx'] = $reData['to_id'];
					$cmfData['opend'] = $reData['form_id'];
					$cmfData['status'] = 1;
					$cmfData['addTime'] = 1;
					$cmfData['type'] = 3;
					$cmfData['gagTime'] = 0;
					$cmfData['nickName'] = UserModel::where('user_qq',$reData['form_id'])->value('user_nick');
					// $i = ChatGroupMember::insert($cmfData);
					$i = 1;
					if ($i) {
						$rrda['id'] = $reData['form_id'];
						$rrda['type'] = 2;
						$rrda['re_id'] = $data['requestId'];
						return $this->reData(1,'同意成功',$rrda);
					}else{
						throw new Exception("同意失败,请重试");
					}
			} catch (Exception $e){
				return $this->reData(0,$e->getMessage(),[]);
			}
		}
	}

	/**
	 * [cancelfriend 拒绝好友 群]
	 * @return [type] [description]
	 */
	public function cancelfriend(){

		// 获取参数
		$data = $this->request->param();
		// 判断登录是否过期
		if (!$this->isLogin($data)) {
			return 0;die;
		};

		// 数据操作 判断是否有记录
		// 当记录的状态返回 1 的时候  已做处理
		$reData = Db('blog_chat_request')->where([
				're_id' => $data['requestId'],
				'to_id' => $data['opend'],
				'status' => 0,
			])->find();
		if (!$reData) {
			return $this->reData(0, '没有此请求', []);
		}

		// 判断是好友还是群
		$i = ChatRequest::where('re_id',$data['requestId'])->update(['to_status'=>2,'status'=>1,'to_read'=>0,'o_time'=>time()]);
		if ($i) {
			return $this->reData(1,'拒绝成功',[]);
		}else{
			return $this->reData(0,'拒绝失败，请重试',[]);
		}
	}

	/**
	 * [findFriendTotal 查询好友 群组]
	 * @return [type] [description]
	 */
	public function findfriendtotal(){
		$data = $this->request->param();
		if (!$this->isLogin($data)) {
			return 0;die;
		};
		// 查询好友以及群组
		if($data['type'] == 'friend'){
			// 这里应该做一个 判断处理  昵称以及编号的查询
			$serachData = UserModel::where('user_nick','like','%'.$data['id'].'%')->where('is_status',1)->field('user_qq as id,user_nick as name,is_chat_sign as text,user_img as img')->select();
		}else if($data['type'] == 'group'){
			$serachData =  ChatGroupModel::where('groupName', 'like' ,'%'.$data["id"].'%')->field('groupIdx as id,groupName as name,des as text,group_img as img')->select();
		}
		return $this->reData(1,'',$serachData);
	}

	/**
	 * [creategroup 创建群]
	 * @return [type] [description]
	 */
	public function creategroup(){

		$data = $this->request->param();
		if (!$this->isLogin($data)) {
			return 0;die;
		};
		/**
		 * 做用户处理 每个用户不可以创建群超过三个
		 */
		$groupCount = ChatGroupModel::where('belong',$data['opend'])->count();
		if ($groupCount > 3) {
			return $this->reData(0, "每个用户只能创建三个群", []);die;
		}
		$dataGroup = json_decode($data['data'] ,true);
		$dataGroup['group_img'] = 'http://test.guoshanchina.com/uploads/person/911058.jpg';
		$dataGroup['status'] = 1;
		// $i = ChatGroupModel::insert($dataGroup);
		$i = 1;
		if ($i) {
			return $this->reData(1,"创建群成功",[]);
		}else{
			return $this->reData(0,"创建群失败",[]);
		}
	}


	/**
	 * [grouplist 请求组的数据]
	 * @return [type] [description]
	 */
	public function grouplist(){

		$data = $this->request->param();
		if (!$this->isLogin($data)) {
			return 0;die;
		};

		$data = ChatMyGroupModel::where('opend',$data['opend'])->field('mygroupIdx,mygroupName')->order('weight desc')->select();
		return $data;
	}

	/**
	 * [addrequest 添加好友或者群组]
	 * @return [type] [description]
	 */
	public function addrequest(){

		$data = $this->request->param();
		if (!$this->isLogin($data)) {
			return 0;die;
		};

		// 做判断
		$userFriend = Db::query('SELECT * from blog_chat_my_group a inner JOIN blog_chat_my_friend b ON a.mygroupIdx = b.mygroupIdx where a.opend = "'.$data['opend'].'" and b.opend = "'.$data['to_id'].'"');
		if ($userFriend) {
			return $this->reData(0,'不可以重复添加好友',[]);
		}
		// form_id
		// to_id
		// postscript
		// c_time
		// status
		// group_id
		$groupid = $data['groupid'];
		/**
		 * 1 如果是群的操作
		 * 2 先判断群是否需要群主审批后加入
		 * 2.1 不用直接返回  加入群 
		 * 2.2 需要添加数据
		 */
		if (empty($data['groupid']) && strlen($data['to_id']) < 33) {
			// 判断是否在群了面
			$igu = ChatGroupMember::where([
						'groupIdx' => $data['to_id'],
						'opend' => $data['opend'],
						])->count();
			if ($igu) {
				return $this->reData(0,'已是群成员',[]);
			}

			$dataGroup = ChatGroupModel::where('groupIdx',$data['to_id'])->field('number,approval')->find();
			if (!$dataGroup) {
				return $this->reData(0,'该群已解散',[]);
			}
			$userCount = ChatGroupMember::where('groupIdx',$data['to_id'])->count();
			$dataGroup = $dataGroup->toArray();
			if ($userCount >= $dataGroup['number']) {
				return $this->reData(0,'该群已满人',[]);
			}
			if ($dataGroup['approval'] != 1) {
				// 不需要验证  直接通过
				Db::startTrans();
				try{
					// 添加进群
					$cmfData['groupIdx'] = $data['to_id'];
					$cmfData['opend'] = $data['opend'];
					$cmfData['status'] = 1;
					$cmfData['addTime'] = 1;
					$cmfData['type'] = 3;
					$cmfData['gagTime'] = 0;
					$cmfData['nickName'] = UserModel::where('user_qq',$data['opend'])->value('user_nick');
					// $i = ChatGroupMember::insert($cmfData);
					$i = 1;
					// 做申请记录
					$rData['form_id'] = $data['opend'];
					$rData['to_id'] = $data['to_id'];
					$rData['c_time'] = time();
					$rData['o_time'] = time();
					$rData['status'] = 1;
					$rData['postscript'] =  $data['postscript'];
					$rData['to_status'] = 1;
					$rData['to_read'] = 1;
					// $r = ChatRequest::insert($rData);
					$r = 1;
					// 返回的数据
					$groupDD = ChatGroupModel::where("groupIdx",$data['to_id'])->field('groupName,group_img')->find();
					$gList['type'] = 'group';
					$gList['avatar'] = $groupDD['group_img'];
					$gList['groupname'] = $groupDD['groupName'];
					$gList['id'] = $data['to_id'];

					if ($i && $r) {
						Db::commit();
						return $this->reData(2,'加入群成功',$gList);
					}else{
						Db::rollback();
						throw new Exception("加入群失败");
					}
				} catch (Exception $e){
						return $this->reData(0,$e->getMessage(),[]);
				}
			}

			$pp = Db::query('SELECT a.user_qq FROM blog_user a RIGHT JOIN blog_chat_group b ON b.belong = a.user_id WHERE b.groupIdx = '.$data['to_id']);
			$groupid= $pp[0]['user_qq'];
			// 群的情况下  保存type
			$map['type'] = 2;
		}
		$map['form_id'] = $data['opend'];
		$map['to_id'] = $data['to_id'];
		$map['c_time'] = time();
		$map['status'] = 0;
		$map['group_id'] = $groupid;
		$map['postscript'] = $data['postscript'];
		$i = Db('blog_chat_request')->insert($map);
		if ($i) {
			return $this->reData(1,'申请成功',[]);
		}else{
			return $this->reData(0,'申请失败。。',[]);
		}
	}

	/**
	 * [crequst 信息盒子的数据]
	 * @return [type] [description]
	 */
	public function crequst(){
		// ChatRequest
		$data = $this->request->param();
		if (!$this->isLogin($data)) {
			return 0;die;
		};

		$reDa['data'] = ChatRequest::cboxmsg($data['opend']);
		$reDa['group'] = '';
		if ($reDa['data']) {
			$reDa['group'] = ChatMyGroupModel::where('opend',$data['opend'])->field('mygroupIdx,mygroupName')->order('weight desc')->select();
		}
		return $this->reData(1,'获取成功',$reDa);
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