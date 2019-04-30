<?php
namespace app\common\model;

use think\Db;
use think\Model;
use app\common\model\CateModel;

/**
*  文章分类的模型
*/
class ArticleModel extends Model
{
	
	protected $table = 'blog_article';

	public function getArticleTimeAttr($value){
		return date("Y-m-d h:i:s",$value);
	}

	public function cateData(){
		return $this->hasOne('CateModel','cate_id','cate_id');
	}

	public static function articlelist($param){

		$page = empty($param['page']) ? 1 : $param['page'];
		$limit = empty($param['limit']) ? 20 : $param['limit'];
		$where = "article_is_del = 2";
		$key = [];
		if (!empty($param['key'])) {
			$key = $param['key'];
		}
		if (!empty($key['title'])) {
			$where .= " and article_title like '%{$key['title']}%'";
		}
		if (!empty($key['start']) || !empty($key['end'])) {
			$start = empty($key['start']) ? strtotime(date("Y-m-d").' 00:00:00') : strtotime($key['start'].' 00:00:00');
			$end = empty($key['end']) ? strtotime(date("Y-m-d").' 23:59:59') : strtotime($key['end'].' 23:59:59');
			$where .= " and article_time > {$start} and article_time < {$end} ";
		}
		$data['data'] = self::with([
						'cateData'=>function($cate){
							$cate->field('cate_id,cate_name');
						}
					])
					->where($where)
					->field('article_id,article_title,article_img,article_nick,article_is_state,article_hot,admin_id,article_time,cate_id,read_sum,click_sum,is_comment')
					->select();

		$data['count'] = self::where('article_is_del' , 2)->count();
		return $data;
	}


	// 配合前台显示
	public function cateApiData($type = 0){
			return $this->hasOne('CateModel','cate_id','cate_id');
	}
	/**
	 * [articleSel 前台分类出来的]
	 *
	 * 
		$cate_id = CateModel::where('cate_name', $type)->where('is_state', 1)->value('cate_id');
		$data = ArticleModel::where('cate_id',$cate_id)->where('article_is_del', 2)->field('article_id,article_title,article_img,article_nick,article_time')->select()->toArray();

	 * @return [type] [description]
	 */
	public static function articleSel($type = 0){
		if (!empty($type)) {
			$data = Db::query("SELECT a.article_id,a.article_title,a.article_img,a.article_nick,a.article_time,b.cate_name FROM blog_article a INNER JOIN blog_cate b ON b.cate_id = a.cate_id WHERE b.cate_name = '{$type}' ORDER BY a.article_time DESC");
		}else{
			$data = Db::query("SELECT a.article_id,a.article_title,a.article_img,a.article_nick,a.article_time,b.cate_name FROM blog_article a INNER JOIN blog_cate b ON b.cate_id = a.cate_id ORDER BY a.article_time DESC");
		}
		return $data;
	}
}