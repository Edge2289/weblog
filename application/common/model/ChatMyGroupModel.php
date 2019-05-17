<?php
namespace app\common\model;

use think\Model;
use app\common\model\ChatMyFriendModel;

/**
*  我的朋友 分组
*/
class ChatMyGroupModel extends Model
{
	protected $table = 'blog_chat_my_group';
	protected $hidden = ['opend','weight'];

	public function friend(){
		return $this->hasMany('ChatMyFriendModel','mygroupIdx','mygroupIdx');
	}

	public static function groupfriend($opend){
		// 2CB99992FE060C4B897B0E9419887AC8
		$data = self::with(['friend','friend.user'])
					->where('opend', $opend)
					->order('weight asc')
					->select()
					->toArray();

		$groupData = [];
		foreach ($data as $gk => $gv) {
			
			$groupData[$gk]['groupname'] = $gv['mygroupName'];
			$groupData[$gk]['id'] = $gv['mygroupIdx'];
			if (!empty($gv['friend'])) {
				foreach ($gv['friend'] as $fk => $fv) {
					$groupData[$gk]['list'][$fk]['username'] = $fv['nickName'];
					$groupData[$gk]['list'][$fk]['id'] = $fv['opend'];
					$groupData[$gk]['list'][$fk]['avatar'] = empty($fv["user"]['user_img'])?'//tva4.sinaimg.cn/crop.0.1.1125.1125.180/475bb144jw8f9nwebnuhkj20v90vbwh9.jpg':$fv["user"]['user_img'];
					$groupData[$gk]['list'][$fk]['sign'] = $fv['nickName']."说： 我很快乐 !";
					$groupData[$gk]['list'][$fk]['status'] = $fv["user"]['is_chat_status'];
				}
			}
		}
		return $groupData;
	}

	public static function friendList($opend){
		// 2CB99992FE060C4B897B0E9419887AC8
		$data = self::with(['friend','friend.user'])
					->where('opend', $opend)
					->where('is_chat_status', 1)
					->order('weight asc')
					->select()
					->toArray();

		$groupData = [];
		foreach ($data as $gk => $gv) {
			
			if (!empty($gv['friend'])) {
				foreach ($gv['friend'] as $fk => $fv) {
					$groupData[] = $fv['opend'];
				}
			}
		}
		return $groupData;
	}

}