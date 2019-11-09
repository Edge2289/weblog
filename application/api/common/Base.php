<?php
namespace app\api\common;

use app\common\middleware\Apimiddleware;
use think\Request;
use think\Session;
use think\Controller;
use ZipArchive;
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

    public function middleware(){
        $request = Request();
	    if(!$request->isPost()){
            return false;
        }
        // post请求验证
        $apiMiddApim = new Apimiddleware();
        // 签名验证
        if(($msg = $apiMiddApim->handle($request->param())) !== true){
            dd([
                'code' => '-1',
                'msg' => $msg,
                'data' => []
            ]);
        }
    }
}