<?php
namespace app\index\controller;

use think\Db;
use think\Controller;

class Callback extends Controller
{
    //访问QQ登录页面
    public function qqlogin(){
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
        
        $token = md5(md5($openid).time());  // 设置登录token
        $findId = Db('blog_user')->where('user_qq',$openid)->find();
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
                Db('blog_user')->insert($map);
                Db('blog_chat_group_member')->insert([
                        'groupIdx' => 1,
                        'opend' => $openid,
                        'status' => 1,
                        'addTime' => time(),
                        'type' => 3, // 用户
                        'gagTime' => 0,
                        'nickName' => $userinfo["nickname"],
                    ]);
            }
            $is_comment = empty($findId) ? 1 : $findId['is_comment'];
            // 重定向回去前端
            $mdp = 'user_qq='.$openid.'&user_nick='.$userinfo["nickname"].'&user_img='.$userinfo["figureurl_2"].'&access_token='.$token.'&is_comment='.$is_comment;
        $this->redirect('http://blog.uikiss.cn?time='.base64_encode($mdp));
    }

    public function __empty(){
        $this->redirect("http://baidu.com");
    }

}
