<?php
namespace app\index\common;

use think\Db;
use think\Config;
use think\Request;
use think\Session;
use think\Controller;

/**
*  模板的基类
*/
class Base extends Controller
{
	// 管理员ID
	protected $adminId;

	// 

	public function __construct()
	{
		$request = Request::instance();
		// 判断域名
		if(!strpos($request->domain(),Config('url'))){
			$this->redirect("http://www.uikiss.cn");
		}
		
		parent::__construct();
		$this->isLogin();
		$this->assign('nav' , Config::get('nav'));
		$this->assign('title' , '后台系统');
		$this->assign('adminTitle' , Db('blog_sys')->where('name','adminTitle')->value('value'));
	}

	public function isLogin(){
		$adminData = Session('adminData.admin_id');
		if (empty($adminData)) {
			return $this->redirect('login/index');
		}
		$this->adminId = Session('adminData.admin_id');
	}
}