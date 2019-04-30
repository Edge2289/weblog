<?php
namespace app\api\controller;

use think\Db;
use app\api\common\Base;
use app\common\model\BannerModel;
/**
* 
*/
class admin extends Base
{
	/**
	 * [bannerlist 列表]
	 * @return [type] [description]
	 */
	public function bannerlist(){
		$banerModel = new BannerModel();
		$data =  $banerModel->sel()->toArray();
		return [
		  "code" => 0,
		  "msg"=> "", 
		  "count" => 1, 
		  "data"=> $data
		];
	}

	/**
	 * [adminlist 管理元列表]
	 * @return [type] [description]
	 */
	public function adminlist(){
		$data = Db('blog_admin')->select();
		return [
		  "code" => 0,
		  "msg"=> "", 
		  "count" => 1, 
		  "data"=> $data
		];
	}
}