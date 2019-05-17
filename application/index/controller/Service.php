<?php

use think\Db;
use app\common\model\UserModel;
use app\common\model\ChatMyGroupModel;
use app\common\model\ChatGroupMember;
/**
*  swoole ： 服务端
*  name : 小小的成
*/
class Service
{
	const swoole_host = '0.0.0.0'; // swoole  服务器地址
	const swoole_part = 7326; // 端口号
	private $server = null;  // websocket 创建单例对象
	private $connectList = []; // fd 的集合 用redis 来作为缓存

	const redis_host = '127.0.0.1'; // redis  服务器地址
	const redis_part = 6379; // 端口号
	private $cli = null; // redis 的单例对象

	// private $redisOpend = null; // 启用会使自己无法发送消息

	public function __construct()
	{
		// 实例化swoole 单例
		$this->server = new \swoole_websocket_server(self::swoole_host, self::swoole_part);
		// 实例化redis 单例
		$this->cli = new Redis();
		$this->cli->connect(self::redis_host, self::redis_part);
		$this->cli->select(8);
		// 清楚全部的fd
		$this->cli->flushdb();
        //监听连接事件
        $this->server->on('open', [$this, 'onOpen']);
        //监听接收消息事件
        $this->server->on('message', [$this, 'onMessage']);
        //监听关闭事件
        $this->server->on('close', [$this, 'onClose']);
		//设置允许访问静态文件
        // $this->server->set([
        //     'document_root' => '/grx/swoole/public',//这里传入静态文件的目录
        //     'enable_static_handler' => true//允许访问静态文件
        // ]);
        $this->server->start();
	}

	/**
	 * [sendMessage 发送信息回客户端]
	 * @param  [type]  $server      [服务]
	 * @param  [type]  $uid         [推送者opend]
	 * @param  [type]  $data        [数据]
	 * @param  boolean $offline_msg [是否保存在数据库]
	 * @return [type]               [description]
	 */
	public function sendMessage($server ,$opend ,$data ,$offline_msg = false){
		$fd = $this->cli->get('opend:'.$opend);
		// 用户离线
		if ($fd == false) {
			if ($offline_msg) {
				$data = [
					'opend' => $opend,
					'data' => json_encode($data),
					'status' => 0,
				];
				// 数据插入
				Db('blog_chat_offline_message')->insert($data);
			}
			return false;
		}
		$this->server->push($fd, json_encode($data));
	}

	/**
	 * [onOpen 链接开启时]
	 * @param  [type] $server  [description]
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function onOpen($server, $request){
		// 这里做 swoole fd 于用户的opend 连接起来 
		// 用redis 做保存
		$requestData = $request->get;
		if (!isset($requestData['opend'])) {
			// 参数错误  没有登录
			// 中断连接
			$data = [
				"type" => "tokenerror"
			];
			$this->server->push($request->fd, json_encode($data));
			return ;
		}
		$i = UserModel::where('user_qq',$requestData['opend'])->find();
		if(empty($i)){
			$data = [
				"type" => "tokenerror"
			];
			$this->server->push($request->fd, json_encode($data));
			return ;
		}
		$this->cli->set('opend:'.$requestData['opend'], $request->fd);
		$this->cli->set('fd:'.$request->fd, 'opend:'.$requestData['opend']);
		// 更改mysql 的在线状态
		// 获取用户的好友 发送在线状态
		$friendData = ChatMyGroupModel::friendList($requestData['opend']);
		// 前端数据更新
		$data = [
			'opend' => $requestData['opend'],
			'status' => "online",
		];
		foreach ($friendData as $key => $value) {
			// 推送消息
			$this->sendMessage($server, $value, ['emit'=>'friendStatus', 'data'=>$data]);
		}

		// 获取离线消息
		$offData = Db('blog_chat_offline_message')
						->where('opend',$requestData['opend'])
						->where('status',0)
						->select();
		foreach ($offData as $ok => $ov) {
			$i = $this->sendMessage($server, $requestData['opend'], json_decode($ov['data'], true));
			// if ($i) {
				// 不管是否推送成功  都将离线数据写入读取状态
				Db('blog_chat_offline_message')->where('offline_id',$ov['offline_id'])->update(['status'=>1]);
			// }
		}
		Db('blog_user')->where('user_qq',$opend_id)->update(['is_chat_status' => 0]);
	}

	/**
	 * [onMessage 接收到消息]
	 * @param  [type] $server  [description]
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function onMessage($server, $request){

		/**
		 * 接收到的数据
		 */
		$data = json_decode($request->data, true);
		/**
		 *  判断接受的信息类型
		 *  进行不同的处理
		 */
		switch ($data['type']) {
			case 'ping':
				break;
			// 聊天信息
			case 'chatMessage':
				// 判断是群还是个人好友
				if ($data['data']['to']['type'] == 'friend') {
					// 判断发送的对象是不是自己
					if ($data['data']['to']['id'] == $data['data']['mine']['id']) {
						// 如果使用全局定义的变量  会实现别人发送不了信息给最后一个登陆的人
						return ;
					}
					// 规定发送信息的格式
					$sendData = [
                        'username' => $data['data']['mine']['username'],//消息来源用户名
                        'avatar' => $data['data']['mine']['avatar'],//消息来源用户头像
                        'id' => $data['data']['mine']['id'],  //消息的来源ID（如果是私聊，则是用户id，如果是群聊，则是群组id）
                        'type' => $data['data']['to']['type'],  //聊天窗口来源类型，从发送消息传递的to里面获取
                        'content' => $data['data']['mine']['content'],  //消息内容
                        'cid' => 0,  //消息id，可不传。除非你要对消息进行一些操作（如撤回）
                        'mine'=> false,//$this->redisOpend == $data['data']['to']['id'] ? true : false,//要通过判断是否是我自己发的
                        'fromid' => $data['data']['mine']['id'],  //消息的发送者id（比如群组中的某个消息发送者），可用于自动解决浏览器多窗口时的一些问题
                        'timestamp' => time()*1000 //服务端时间戳毫秒数
					];
					$this->sendMessage($server, $data['data']['to']['id'], ['emit'=>'chatMessage', 'data'=>$sendData] , true);
					// 做聊天记录
					// mysql 保存
                    $record_data = [
                        'from'       => $data['data']['mine']['id'],
                        'to'     => $data['data']['to']['id'],
                        'content'       => $data['data']['mine']['content'],
                        'type' => 'friend',
                        'sendTime'    => time(),
                        'status'     => 1,
                    ];
                    $bcc = DB::table('blog_chat_chatlog')->insert($record_data);
                    print_r("记录保存状态：".$bcc);

				}else if ($data['data']['to']['type'] == 'group') {
					// 群组的id 用户获取群的用户 来做发送信息
					$groupId = $data->data->to->id;
					$groupData = ChatGroupMember::where('groupIdx',$groupId)
									->where('status',1)
									->field('opend')
									->select();
					// 定义发送的信息
					$sendData = [
                        'username' => $data['data']['mine']['username'],//消息来源用户名
                        'avatar' => $data['data']['mine']['avatar'],//消息来源用户头像
                        'id' => $data['data']['to']['id'],  //消息的来源ID（如果是私聊，则是用户id，如果是群聊，则是群组id）
                        'type' => $data['data']['to']['type'],  //聊天窗口来源类型，从发送消息传递的to里面获取
                        'content' => $data['data']['mine']['content'],  //消息内容
                        'cid' => 0,  //消息id，可不传。除非你要对消息进行一些操作（如撤回）
                        'mine'=> false,//要通过判断是否是我自己发的
                        'fromid' => $data['data']['mine']['id'],  //消息的发送者id（比如群组中的某个消息发送者），可用于自动解决浏览器多窗口时的一些问题
                        'timestamp' => time()*1000 //服务端时间戳毫秒数
					];
					foreach ($groupData as $key => $value) {
						if ($value['opend'] == $this->redisOpend) {
							continue;
						}
						$this->sendMessage($server, $value['opend'], ['emit'=>'chatMessage', 'data'=>$sendData], true);
					}
					// 做聊天记录
					// mysql 保存
                    $record_data = [
                        'from'       => $data['data']['mine']['id'],
                        'to'     => $data['data']['to']['id'],
                        'content'       => $data['data']['mine']['content'],
                        'type' => 'group',
                        'sendTime'    => time(),
                        'status'     => 1,
                    ];
                    DB::table('blog_chat_chatlog')->insert($record_data);
				}

				break;
			// 用户状态修改
			case 'changStatus':

				$friendData = ChatMyGroupModel::friendList($data['data']['opend']);
				// 前端数据更新
				$data = [
					'type' => 'changStatus',
					'opend' => $data['data']['opend'],
					'status' => $data['data']['status'],
				];
				foreach ($friendData as $key => $value) {
					// 推送消息
					$this->sendMessage($server, $value, $data);
				}
				break;

			default:
				# code...
				break;
		}

	}

	/**
	 * [onClose 关闭连接]
	 * @param  [type] $server [description]
	 * @param  [type] $fd     [description]
	 * @return [type]         [description]
	 */
	public function onClose($server, $fd){
		// 用户下线 把用户的fd 取消关联在redis 上面
		$opend = $this->cli->get('fd:'.$fd);
		// 删除
		$this->cli->del('fd:'.$fd);
		$this->cli->del('opend:'.$opend);
		// 拆分open id 获取里面的opend 发送好友我下线消息 opend:2CB99992FE060C4B897B0E9419887AC8,
		$opend_id = substr($opend ,6);
		// 查询
		$friendData = ChatMyGroupModel::friendList($opend_id);
		// 前端数据更新
		$data = [
			'opend' => $opend_id,
			'status' => "offline",
		];
		foreach ($friendData as $key => $value) {
			// 推送消息
			$this->sendMessage($server, $value, ['emit'=>'friendStatus', 'data'=>$data]);
		}
		Db('blog_user')->where('user_qq',$opend_id)->update(['is_chat_status' => 0]);
	}
}
$ser = new Service();
?>