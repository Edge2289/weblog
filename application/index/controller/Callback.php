<?php
namespace app\index\controller;

use think\Db;
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
        
        /**
         *  1 先判断id是否登记过
         *  -1 有id直接登录
         *  -3 没有注册 + 登录
         */
        
        $token = md5(md5($findId).time());  // 设置登录token
        $findId = Db('blog_user')->where('user_qq',$openid)->select();
        if ($findId) {
            // 登录
            Db('blog_user')->where('user_qq',$openid)->update([
                    'access_token' => $token
                ]);
            
        }else{
                // 注册
                $map['user_qq'] = $openid;
                $map['user_nick'] = $userinfo["nickname"];
                $map['user_img'] = $userinfo["figureurl_2"];
                $map['access_token'] = $token;
                $map['is_status'] = 1;
                $map['register_time'] = time();
                $map['is_comment'] = 1;
            }
        $this->redirect('http://blog.uikiss.cn',[
                'user_qq'=>$openid,
                'user_nick'=>$userinfo["nickname"],
                'user_img'=>$userinfo["figureurl_2"],
                'access_token'=>$token,
                'is_comment'=>empty($findId) ? 1 : $findId['is_comment'],
                ]);
    }
 
 
 
}