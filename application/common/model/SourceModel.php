<?php
namespace app\common\model;

use think\Model;

/**
*  访客数据统计表
*/
class SourceModel extends Model
{
	
	protected $table = "blog_source";


	/**
	 * [source 访问量 pv]
	 * @return [type] [description]
	 */
	public static function sourcelist($param){
		$where = 'source_time > '.$param["start"].' and source_time < '.$param["end"];

		$data['list'] = self::where($where)->page($param['page'],$param['limit'])->order('source_id desc')->select()->toArray();
		$data['count'] = self::where($where)->count();
		return $data;
	}

	/**
	 * [iplist ip 访问量]
	 * @return [type] [description]
	 */
	public static function iplist($param){
		$where = 'source_time > '.$param["start"].' and source_time < '.$param["end"];

		$data['list'] = self::where($where)
						->group('source_ip')
						->page($param['page'],$param['limit'])
						->order('source_id desc')
						->select()
						->toArray();
		$data['count'] = self::where($where)
						->group('source_ip')->count();
		return $data;

	}

	/**
	 * [uvlist uv 访问量 数据]
	 * @return [type] [description]
	 */
	public function uvlist($param){

	}

	/**
	 * [articletjlist 访问文章]
	 * @return [type] [description]
	 */
	public static function articlelist($param){
		$where = 'source_url like "%details.html?arti%" and';
		$where .= ' source_time > '.$param["start"].' and source_time < '.$param["end"];
		$data['list'] = self::where($where)->page($param['page'],$param['limit'])->order('source_id desc')->select()->toArray();
		$data['count'] = self::where($where)->count();
		return $data;
	}
}