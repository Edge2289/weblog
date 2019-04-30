<?php
namespace app\index\controller;

use think\Request;
use think\Session;
use think\Controller;
use app\index\model\AdminModel;
/**
*  登录页面
*/
class Login extends Controller
{
	
	/**
	 * [index 登录页面]
	 * @return [type] [description]
	 */
	public function index(){

		$this->assign('title','后台登录');
		return $this->fetch();
	}

	/**
	 * [isSubmit 登录验证]
	 * @return boolean [description]
	 */
	public function isSubmit(Request $request){

		if(!$request->isPost()){
			return DataReturn("-1","提交方式错误",[]);
		}
		$data = $request->param();
		// 判断用户名是否错误
		if(!preg_match("/^[a-zA-Z\s]+$/",$data['username'])){
			return DataReturn("-1","用户名格式错误",[]);
		}

		$am = new AdminModel();
		return $am->select($data);
	}

	/**
	 * [outLogin 注销页面]
	 * @return [type] [true]
	 */
	public function outLogin(){

		Session::clear();
		$this->redirect('login/index');
	}

}