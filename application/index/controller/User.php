<?php
namespace app\index\controller;

use app\index\common\Base;

/**
*  用户管理
*/
class User extends Base
{
	
	public function index(){
		
		return $this->fetch();
	}
}