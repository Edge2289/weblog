<?php
namespace app\index\common;

use think\Config;
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
		parent::__construct();
		$this->isLogin();
		$this->assign('nav' , Config::get('nav'));
		$this->assign('title' , '后台系统');
	}

	public function isLogin(){

		if (empty(Session('adminData.admin_id'))) {
			return $this->redirect('login/index');
		}
		$this->adminId = Session('adminData.admin_id');
	}
}