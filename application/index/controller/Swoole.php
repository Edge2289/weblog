<?php
namespace app\index\controller;

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
		$this->server = new swoole_websocket_server(self::swoole_host, self::swoole_part);
		// 实例化redis 单例
		$this->cli = new Redis();
		$this->cli->connect(self::redis_host, self::redis_part);
		$this->cli->select(8);
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
	 * [onOpen 链接开启时]
	 * @param  [type] $server  [description]
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function onOpen($server, $request){
		// 这里做 swoole fd 于用户的opend 连接起来 
		// 用redis 做保存
		print_r($request);
		dd(UserModel::where(1)->select());
	}

	/**
	 * [onMessage 接收到消息]
	 * @param  [type] $server  [description]
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function onMessage($server, $request){

	}

	/**
	 * [onClose 关闭连接]
	 * @param  [type] $server [description]
	 * @param  [type] $fd     [description]
	 * @return [type]         [description]
	 */
	public function onClose($server, $fd){

	}
}
$ser = new Service();
?>