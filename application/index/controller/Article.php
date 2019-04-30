<?php
namespace app\index\controller;

use think\Request;
use app\index\model\ArticleModel;
use app\index\model\CateModel;
use app\index\common\Base;

/**
*  文章管理组
*/
class Article extends Base
{

	public function __construct(){

		// 调用父类
		parent::__construct();
		$this->requset = Request::instance();

	}
	
	/**
	 * [articleList 文章的列表]
	 * @return [type] [description]
	 */
	public function articlelist(){

		if ($this->requset->isPost()) {
			dump(123);die;
		}

		return $this->fetch();
	}

	// 文章的添加
	public function articleadd(){
		if ($this->request->isPost()) {
			$data = $this->requset->param();
			return ArticleModel::ins($data['data'], $this->adminId);
		}
		$this->assign('catelist', CateModel::where('is_state',1)->field('cate_name,cate_id')->select());
		return $this->fetch();
	}

	// 文章的修改
	public function articleedit(){

		if ($this->request->isPost()) {
			$data = $this->requset->param();
			return ArticleModel::upd($data['data']);
		}

		$id = input('get.article_id');
		$this->assign('data' , ArticleModel::where('article_id',$id)->find());
		$this->assign('catelist', CateModel::where('is_state',1)->field('cate_name,cate_id')->select());
		return $this->fetch();
	}
	


	/**
	 * [cate 文章的分类 添加]
	 * @return [type] [description]
	 */
	public function cate(){

		if ($this->requset->isPost()) {
			$cate_name = trim(input('post.cate_name'));
			if (empty($cate_name)) {
				echo "<script>alert('请填写正确的分类名');</script>";
			}else{
				$i = CateModel::where('cate_name',$cate_name)->find();
				if ($i) {
					echo "<script>alert('该分类名已存在');</script>";
				}else{
					$map = [
					'cate_name' => $cate_name,
					'cate_sort' => 50,
					'is_state' => 1,
					'is_delete' => 1,
					];
					$i = CateModel::insertGetId($map);
					if (!$i) {
						echo "<script>alert('添加失败');</script>";
					}else{
						echo "<script>alert('添加成功');</script>";
					}
				}
			}
		}

		$cateData = CateModel::select();
		$this->assign('cateData', $cateData);
		return $this->fetch();
	}

	/**
	 * [cateedit 分类修改]
	 * @return [type] [description]
	 */
	public function cateedit(){
		$data = $this->requset->param();
		if ($data['type'] == 'is_state') {
			$array = ['1','2'];
			if (!in_array($data['text'], $array)) {
				return DataReturn('-1', "参数错误", []);
			}
		}else if($data['type'] == 'cate_sort'){
			if ($data['text'] >100 || $data['text'] < 1) {
				return DataReturn('-1', "参数错误", []);
			}
		}
		$i = CateModel::where("cate_id",$data['cate_id'])->update([$data['type'] => $data['text']]);
		if ($i) {
			return DataReturn(0, "修改成功", []);
		}else{
			return DataReturn('-1', "修改失败", []);
		}
	}

	/**
	 * [catedel 分类删除]
	 * @return [type] [description]
	 */
	public function catedel(){
		$cate_id = input('get.cate_id');
		$i = CateModel::where("cate_id",$cate_id)->value('is_delete');
		if ($i == 2) {
			return DataReturn(-1, "该分类不可以删除", []);
		}
		$i = CateModel::where("cate_id",$cate_id)->delete();
		if ($i) {
			return DataReturn(0, "删除成功", []);
		}else{
			return DataReturn('-1', "删除失败", []);
		}
	}
}
