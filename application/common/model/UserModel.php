<?php
namespace app\common\model;

use think\Model;
/**
*  用户
*/
class UserModel extends Model
{
	protected $table = 'blog_user';

	protected function getIsChatStatusAttr($value){
		$statuc = $value == 1 ? 'online' : 'offline';    // offline
		return $statuc ;
	}

	protected $hidden = ['register_time','is_status','is_comment','access_token','user_id'];
}