<?php
namespace app\index\model;

use think\Session;
use think\Model;

/**
*  管理员 的模型
*/
class AdminModel extends Model
{
	
	protected $table = 'blog_admin';

	// 登录验证
	public function select($data){
		// 参数去空值
		$data['username'] = trim($data['username']);
		$data['password'] = trim($data['password']);

		$list = self::where("admin_name",$data['username'])->where('is_state',1)->field('admin_id,admin_nick,admin_pass,loginsum')->find();

		if (empty($list)) {
			return DataReturn("-1","用户名或密码错误 ！",[]);
		}
		if ($list['admin_pass'] != pwmd5($data['password'])) {
			return DataReturn("-1","用户名或密码错误！",[]);
		}

		// 登录成功操作
		// 登录次数自加
		self::where("admin_name",$data['username'])->setInc('loginsum');
		// 存储session
		$userData = [
			'admin_id' => $list['admin_id'],
			'username' => $data['username'],
			'usernick' => $list['admin_nick']
		];
		Session::set("adminData",$userData);
		// 跳转页面
		return DataReturn("0","登录成功",[]);
	}
}