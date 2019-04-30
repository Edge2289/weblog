<?php
namespace app\index\controller;

use think\Request;
use app\common\model\BannerModel;
use app\index\common\Base;

/**
*  banner 图的控制器
*/
class Banner extends Base
{
	
	function __construct()
	{
		parent::__construct();
		$this->requset = Request::instance();
		$this->banerModel = new BannerModel();
	}

	public function index(){

		return $this->fetch();
	}

	/**
	 * [banneradd 添加banner]
	 * @return [type] [description]
	 */
	public function banneradd(){
		
		if ($this->requset->isPost()) {
			$param = $this->requset->param();
			$map = $param['data'];
			unset($map['file']);
			$map['color'] = $map['colorhidden'];
			unset($map['colorhidden']);
			$map['is_state'] = empty($map['is_state']) ? 2 : 1;
			return BannerModel::ine($map);
		}

		return $this->fetch();
	}

	/**
	 * [banneredit 编辑banner]
	 * @return [type] [description]
	 */
	public function banneredit(){

		if ($this->requset->isPost()) {
			$param = $this->requset->param();
			$map = $param['data'];
			unset($map['file']);
			$map['color'] = $map['colorhidden'];
			unset($map['colorhidden']);
			$map['is_state'] = empty($map['is_state']) ? 2 : 1;
			$banner_id = $map['banner_id'];
			unset($map['banner_id']);
			return BannerModel::edit($map, $banner_id);
		}
		$this->assign('data',BannerModel::where('banner_id',input('get.banner_id'))->find());
		return $this->fetch();
	}

	public function bannerdel(){
		if ($this->requset->isPost()) {
			$banner_id = input('post.id');
			return BannerModel::del($banner_id);
		}
	}
}