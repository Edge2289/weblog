<?php
namespace app\common\model;

use think\Model;

/**
*  banner 模型
*/
class BannerModel extends Model
{
	
	protected $table = 'blog_banner';

	public function getBannerTimeAttr($value){
		return date("Y-m-d h:i:s",$value);
	}
	/**
	 * [sel 查询]
	 * @return [type] [description]
	 */
	public function sel($state = 0){
		if (empty($state)) {
			$data = self::select();
		}else{
			$data = self::where('is_state',1)->select();
		}
		return $data;
	}

	/**
	 * [ine 插入]
	 * @return [type] [description]
	 */
	public static function ine($param){

		$param['banner_time'] = time();
		$i = self::insert($param);
		if ($i) {
			return DataReturn(1,'添加成功',[]);
		}else{
			return DataReturn(-1,'添加失败',[]);
		}
	}

	/**
	 * [edit description]
	 * @param  [type] $param     [description]
	 * @param  [type] $banner_id [description]
	 * @return [type]            [description]
	 */
	public static function edit($param ,$banner_id){
		$i = self::where('banner_id', $banner_id)->update($param);
		if ($i) {
			return DataReturn(1,'修改成功',[]);
		}else{
			return DataReturn(-1,'修改失败',[]);
		}
	}

	public static function del($banner_id){
		$i = self::where('banner_id', $banner_id)->delete();
		if ($i) {
			return DataReturn(1,'删除成功',[]);
		}else{
			return DataReturn(-1,'删除失败',[]);
		}
	}
}