<?php
namespace app\index\controller;

use think\swoole\Server;

/**
*  swoole 服务类
*/
class Swoole extends Server
{
	
	protected static $token;
    protected $host = '127.0.0.1';
    protected $port = 9508;
    protected $serverType = 'socket';
    protected static $uid = '';
    protected $option = [
        'worker_num' => 4, // 设置启动的Worker进程数
        'daemonize' => false, //守护进程化。
        'backlog' => 128, //Listen队列长度，
        'dispatch_mode' => 2,
        'heartbeat_check_interval' => 5,
        'heartbeat_idle_time' => 100,
    ];

     //建立连接时回调函数
    public function onOpen($server,$req)
    {
        $fd = $req->fd;//客户端标识
        $uid = $req->get['uid'];//客户端传递的用户id
        $token = $req->get['token'];//客户端传递的用户登录token
        
        //省略token验证逻辑......
        if (!$token) {
            $arr = array('status'=>2,'message'=>'token已过期');
            $server->push($fd, json_encode($arr));
            $server->close($fd);
            return;
        }
        //省略给用户绑定fd逻辑......
        echo "用户{$uid}建立了连接,标识为{$fd}\n";
    }
 
    //接收数据时回调函数
    public function onMessage($server,$frame)
    {
        $fd = $frame->fd;
        $message = $frame->data;
 
        //省略通过fd查询用户uid逻辑......
        $uid = 666;
        $data['uid'] = $uid;
        $data['message'] = '用户'.$uid.'发送了:'.$message;
        $data['post_time'] = date("m/d H:i",time());
        $arr = array('status'=>1,'message'=>'success','data'=>$data);
 
        //仅推送给当前连接用户
        //$server->push($fd, json_encode($arr));
        
        //推送给全部连接用户
        foreach($server->connections as $fd) {
            $server->push($fd, json_encode($arr));
        } 
    }
 
    //连接关闭时回调函数
    public function onClose($server,$fd)
    {
        echo "标识{$fd}关闭了连接\n";
    }

}