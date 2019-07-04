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
	
		$adminData = Session('adminData');
		if (empty($adminData)) {
			dd(['code'=>0, 'msg' => '暫無參數' , 'data' => []]);
		}
		$this->request = Request::instance();
	}

    public function __empty(){
        echo "没有该网站";
    }
}