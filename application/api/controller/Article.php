<?php
namespace app\api\controller;

use think\Request;
use app\api\common\Base;
use app\common\model\ArticleModel;

/**
*  文章api 
*/

class Article extends Base
{
	
	public function __construct(){
		parent::__construct();
	}

	public function articlelist(){
		$list = $this->request->param();
		$data = ArticleModel::articlelist($list);
		return [
		  "code" => 0,
		  "msg"=> "", 
		  "count" => $data['count'], 
		  "data"=> $data['data']->toArray()
		];
	}
}