<?php
namespace app\index\controller;

use think\Request;
use app\index\common\Base;

/**
* Visitor 访客管理
*/
class Visitor extends Base
{
	
	public function __initialize()
	{
		$this->request = Request::instance();
	}

   
   /**
    * [source 用户来源列表]
    * @return [type] [description]
    */
	public function source(){

		return $this->fetch();
	} 

    /**
     * [articletj 文章访问统计]
     * @return [type] [description]
     */
	public function articletj(){

	}
}	