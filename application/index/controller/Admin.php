<?php
namespace app\index\controller;

use app\index\common\Base;

/**
*  管理元
*/
class Admin extends Base
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function index(){

		return $this->fetch();
	}
}