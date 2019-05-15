<?php
namespace app\common\model;

use think\Model;

/**
*  群组
*/
class ChatGroupMember extends Model
{
	
	protected $table = 'blog_chat_group_member';

	public function groupinfo(){
		return $this->hasOne('ChatGroupModel','groupIdx','groupIdx');
	}

	public static function group($opend){

		$data = self::with('groupinfo')
					->where('opend',$opend)->select()->toArray();
		$groupData = [];
		foreach ($data as $key => $value) {
			$groupData[$key]['groupname'] = $value["groupinfo"]["groupName"];
			$groupData[$key]['id'] = $value["groupinfo"]["groupIdx"];
			$groupData[$key]['sign'] = $value["groupinfo"]["des"];
			$groupData[$key]['avatar'] = '//tva4.sinaimg.cn/crop.0.1.1125.1125.180/475bb144jw8f9nwebnuhkj20v90vbwh9.jpg';
		}
		return $groupData;
	}
}