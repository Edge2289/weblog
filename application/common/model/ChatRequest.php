<?php
namespace app\common\model;

use think\Model;
use app\common\model\UserModel;
/**
* 
*/
class ChatRequest extends Model
{
	protected $table = "blog_chat_request";

	public function getCTimeAttr($value){

		$at = time()-$value;
		$text = '';
		if ($at < 300) {
			$text = "刚刚";
		}else if ($at < 600) {
			$text = "10分钟前";
		}else if ($at < 1800) {
			$text = "30分钟前";
		}else if ($at < 3600) {
			$text = "一小时前";
		}else if ($at < 86400) {
			$text = "一天前";
		}else if ($at > 86400) {
			$text = "一天后";
		}

		return $text;
	}

	public function getToStatusAttr($value){
		$text = ['','已同意','已拒绝'];
		return $text[$value];
	}

	public function userhas(){
		return $this->hasOne("UserModel",'user_qq','form_id')->field('user_qq,user_img,user_nick');
	}

	public function usermsghas(){
		return $this->hasOne("UserModel",'user_qq','group_id')->field('user_qq,user_img,user_nick');
	}

	public function grouphas(){
		return $this->hasOne('ChatGroupModel','groupIdx','to_id')->field('groupIdx,groupName');
	}

	public static function cboxmsg($opend){

		// 申请好友
		$request = self::with('userhas')
					->where([
							'to_id' => $opend,
							'status' => 0,
						])
					->field('re_id,c_time,o_time,form_id,postscript,type')
					->select()->toArray();
		// 申请群组
		$find = self::with(['userhas','grouphas'])
					->where([
							'group_id' => $opend,
							'status' => 0,
						])
					->field('re_id,c_time,o_time,form_id,postscript,type,to_id')
					->select()->toArray();
		// 合并数据  //需要按时间排序的话  用冒泡来解决
		$data['request'] = self::bubble_sort(array_merge($request,$find));

		// 申请好友msg
		$friendmsg = self::with('userhas')
					->where([
							'form_id' => $opend,
							'status' => 1,
							'to_read' => 0,
							'type' => 1
						])
					->field('re_id,c_time,o_time,form_id,postscript,type,to_status')
					->select()->toArray();
		// 申请群组的msg
		$groupMsgData = self::with(['usermsghas','grouphas'])
					->where([
							'form_id' => $opend,
							'status' => 1,
							'to_read' => 0,
							'type' => 2
						])
					->field('re_id,c_time,o_time,group_id,postscript,type,to_id,to_status')
					->select()->toArray();

		$data['msg'] = self::bubble_sort(array_merge($friendmsg,$groupMsgData));
		return $data;
	}

	/**
	 * [bubble_sort 冒泡排序]
	 * @return [type] [description]
	 */
	public static function bubble_sort($data){
		
		for ($i=0; $i < count($data); $i++) { 
			for ($y=$i; $y < count($data)-1; $y++) { 
				if ($data[$y]['o_time'] < $data[$y+1]['o_time']) {
					$temp = $data[$y];
					$data[$y] = $data[$y+1];
					$data[$y+1] = $temp;
				}
			}
		}
		return $data;
	}
}