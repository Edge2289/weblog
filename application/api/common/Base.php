<?php
namespace app\api\common;

use think\Request;
use think\Session;
use think\Controller;
/**
*  基类
*/
class Base extends Controller
{
	protected $request = '';
		
	public function __construct(){
		
		if (empty(Session('adminData'))) {
			dd(['code'=>0, 'msg' => '暫無參數' , 'data' => []]);
		}
		$this->request = Request::instance();
	}

}