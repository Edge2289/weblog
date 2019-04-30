<?php
namespace app\index\model;

use app\common\model\ArticleModel as am;

/**
*  文章模型
*/
class ArticleModel extends am
{
	
	public static function ins($data, $adminId){


		$data['admin_id'] = $adminId;
		$data['article_time'] = time();
		$data['article_is_state'] = empty($data['article_is_state']) ? 2 : 1;
		$data['is_comment'] = empty($data['is_comment']) ? 2 : 1;
		$data['article_img'] = empty($data['article_img']) ? "/static/img/default/img-".rand(1,6).".jpg": $data['article_img'];
		unset($data['file']);
		$i = self::insert($data);
		if ($i) {
			return DataReturn(0, '添加成功', []);
		}else{
			return DataReturn(-1, '添加失败', []);
		}
	}

	public static function upd($data){

		$article_id = $data['article_id'];
		unset($data['article_id']);
		unset($data['file']);
		$data['article_is_state'] = empty($data['article_is_state']) ? 2 : 1;
		$data['is_comment'] = empty($data['is_comment']) ? 2 : 1;
		$data['article_img'] = empty($data['article_img']) ? "/static/img/default/img-".rand(1,6).".jpg": $data['article_img'];

		$i = self::where('article_id',$article_id)->update($data);
		if ($i) {
			return DataReturn(0, '修改成功', []);
		}else{
			return DataReturn(-1, '修改失败', []);
		}
	}
}