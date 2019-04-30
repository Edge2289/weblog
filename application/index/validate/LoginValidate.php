<?php
namespace app\index\validate;

use think\Validate;

/**
*  登录验证器
*/
class LoginValidate extends Validate
{
	
	protected $rules = [
		'name' => 'require|max:15',
		'password' => 'require|max:25'
	];

	protected $msg = [];
}