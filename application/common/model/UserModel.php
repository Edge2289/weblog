<?php
namespace app\common\model;

use think\Model;
/**
*  用户
*/
class UserModel extends Model
{
	protected $table = 'blog_user';

	protected $hidden = ['register_time','is_status','is_comment','access_token','user_id'];
}