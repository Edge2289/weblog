<?php
namespace app\index\controller;

use think\Request;
use app\index\common\Base;

/**
*  用户管理
*/
class User extends Base
{
	
	public function index(){
		
		return $this->fetch();
	}

	/**
	 * [userupd 更改状态 属性]
	 * @return [type] [description]
	 */
	public function userupd(){
		if (!Request()->isGet()) {
			return DataReturn(-1,'错误请求',[]);
		}
		$param = Request()->param();
		$user = $param['user_id'];
		unset($param['user_id']);
		$i = Db('blog_user')->where('user_id',$user)->update($param);
		if($i){
			return DataReturn(1,'修改成功',[]);
		}else{
			return DataReturn(-1,'修改失败',[]);
		}
	}
}