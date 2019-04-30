<?php
namespace app\index\controller;

use think\Request;
use app\index\common\Base;

/**
*  系统参数
*/
class System extends Base
{
	
	public function index(){
		$request = Request::instance();
		if ($request->isPost()) {
			$param = $request->param();
			Db('blog_sys')->where('name',$param['name'])->update(['value' => $param['value']]);
			return 1;
		}
		$this->assign('sysData',Db('blog_sys')->select());
		return $this->fetch();
	}
}