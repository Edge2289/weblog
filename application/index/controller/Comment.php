<?php
namespace app\index\controller;

use app\index\common\Base;

/**
*  评论的模块
*/
class Comment extends Base
{
	
	public function index(){
		
		return $this->fetch();
	}

}