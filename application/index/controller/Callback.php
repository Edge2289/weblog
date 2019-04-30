<?php
namespace app\index\controller;

use think\Controller;
class Callback extends Controller
{
    //访问QQ登录页面
    public function qqLogin(){
    	$oauth = new \qq_connect\Oauth();
    	$oauth->qq_login();
    }
 
    //qq回调函数
    public function qqCallback(){
    	//请求accesstoken
    	$oauth = new \qq_connect\Oauth();
    	$accesstoken = $oauth->qq_callback();
    	//获取open_id
    	$openid = $oauth->get_openid();
 
    	//设置有效时长(7天)
    	cookie('accesstoken', $accesstoken, 24*60*60*7);
    	cookie('openid', $openid, 24*60*60*7);
 
        //根据accesstoken和open_id获取用户的基本信息
        $qc = new \qq_connect\QC($accesstoken,$openid);
        $userinfo = $qc->get_user_info();
        var_dump($userinfo);
    }
 
 
 
}