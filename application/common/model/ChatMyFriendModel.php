<?php
namespace app\common\model;

use app\common\model\ChatMyGroupModel;
use app\common\model\UserModel;
use think\Model;

/**
*  我的朋友 分组
*/
class ChatMyFriendModel extends Model
{
	protected $table = 'blog_chat_my_friend';
	protected $hidden = ['mygroupIdx','myfriendIdx'];

	public function user(){

		return $this->hasOne('UserModel','user_qq','opend');
	}


	// public function friendOpend(){
	// 	return hasOne('ChatMyGroupModel','mygroupIdx','mygroupIdx');
	// }

	// 根据用户来查询 那些用户有这个好友
	// public function 
}