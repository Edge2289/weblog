<?php
namespace app\api\controller;

use think\Db;
use think\Request;
use app\api\common\Base;
use app\common\model\BannerModel;
use app\common\model\SourceModel;
/**
*  后台api接口
*/
class admin extends Base
{
	public function __initialize(){
		$this->request = Request::instance();
	}
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
	 * [adminlist 管理员列表]
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

	public function commentlist(){

		$param = $this->request->param();
		// 分页
		$param['page'] = empty($param['page']) ? 1: $param['page'];
		$param['limit'] = empty($param['limit']) ? 10: $param['limit'];

		// 时间条件
		$param['start'] = empty($param['start']) ? 1: strtotime($param['start']);
		$param['end'] = empty($param['end']) ? strtotime(date("Y-m-d ")."23:59:59"): strtotime($param['end']);
		$where = " a.comment_time > {$param['start']} and a.comment_time < {$param['end']}";

		$data = Db::query('select a.*,b.comment_val as target_val,c.user_nick from blog_comment a left join blog_comment b on b.comment_id = a.target_id left join blog_user c on c.user_id = a.user_id where '.$where.' order by a.comment_id desc limit '.($param['page']-1).','.($param['page']*$param['limit']));
		
		$count = Db::query('select count(*) from blog_comment a where '.$where);
		return [
		  "code" => 0,
		  "msg"=> "", 
		  "count" => $count[0]['count(*)'], 
		  "data"=> $data
		];
	}

	/**
	 * [userlist 用户列表数据]
	 * @return [type] [description]
	 */
	public function userlist(){
		$param = $this->request->param();
		// 分页
		$param['page'] = empty($param['page']) ? 1: $param['page'];
		$param['limit'] = empty($param['limit']) ? 10: $param['limit'];

		$data = Db('blog_user')->page($param['page'],$param['limit'])->order('user_id desc')->select();
		return [
		  "code" => 0,
		  "msg"=> "", 
		  "count" => Db('blog_user')->count(), 
		  "data"=> $data
		];
	}

	/**
	 * [sourcelist 访问数据统计]
	 * @return [type] [description]
	 */
	public function sourcelist(){

		$param = $this->request->param();
		// 分页
		$param['page'] = empty($param['page']) ? 1: $param['page'];
		$param['limit'] = empty($param['limit']) ? 10: $param['limit'];
		// 时间条件
		$param['start'] = empty($param["key"]['start']) ? 1: strtotime($param["key"]['start']);
		$param['end'] = empty($param["key"]['end']) ? strtotime(date("Y-m-d ")."23:59:59"): strtotime($param["key"]['end']."23:59:59");

		$data = SourceModel::sourcelist($param);
		return [
		  "code" => 0,
		  "msg"=> "", 
		  "count" => $data['count'], 
		  "data"=> $data['list']
		];
	}


	/**
	 * [sourcelist 访问文章统计]
	 * @return [type] [description]
	 */
	public function articletjlist(){

		$param = $this->request->param();
		// 分页
		$param['page'] = empty($param['page']) ? 1: $param['page'];
		$param['limit'] = empty($param['limit']) ? 10: $param['limit'];
		// 时间条件
		$param['start'] = empty($param["key"]['start']) ? 1: strtotime($param["key"]['start']);
		$param['end'] = empty($param["key"]['end']) ? strtotime(date("Y-m-d ")."23:59:59"): strtotime($param["key"]['end']."23:59:59");

		$data = SourceModel::articlelist($param);
		return [
		  "code" => 0,
		  "msg"=> "", 
		  "count" => $data['count'], 
		  "data"=> $data['list']
		];
	}


	public function iplist(){
		$param = $this->request->param();
		// 分页
		$param['page'] = empty($param['page']) ? 1: $param['page'];
		$param['limit'] = empty($param['limit']) ? 10: $param['limit'];
		// 时间条件
		$param['start'] = empty($param["key"]['start']) ? 1: strtotime($param["key"]['start']);
		$param['end'] = empty($param["key"]['end']) ? strtotime(date("Y-m-d ")."23:59:59"): strtotime($param["key"]['end']."23:59:59");

		$b = SourceModel::iplist($param);
		dd($b);
	}
	/**
	 * [userloglist 用户登录统计]
	 * @return [type] [description]
	 */
	public function userloglist(){

		$param = $this->request->param();
		// 分页
		$param['page'] = empty($param['page']) ? 1: $param['page'];
		$param['limit'] = empty($param['limit']) ? 10: $param['limit'];
		// 时间条件
		$param['start'] = empty($param["key"]['start']) ? 1: strtotime($param["key"]['start']);
		$param['end'] = empty($param["key"]['end']) ? strtotime(date("Y-m-d ")."23:59:59"): strtotime($param["key"]['end']."23:59:59");

		$where = 'time > '.$param["start"].' and time < '.$param["end"];

		$data['list'] = Db('blog_userlogin_log')
						->alias('a')
						->join("blog_user b","a.user_qq = b.user_qq",'left')
						->where($where)
						->page($param['page'],$param['limit'])
						->order('log_id desc')
						->field('a.*,b.user_nick')
						->select();
		$data['count'] = Db('blog_userlogin_log')
						->where($where)
						->count();
		return [
		  "code" => 0,
		  "msg"=> "", 
		  "count" => $data['count'], 
		  "data"=> $data['list']
		];			
	}
}