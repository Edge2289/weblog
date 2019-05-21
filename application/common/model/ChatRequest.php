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


	public static function cboxmsg($opend){

		$data['request'] = self::with('userhas')
					->where([
							'to_id' => $opend,
							'status' => 0,
						])
					->field('re_id,c_time,form_id,postscript')
					->select()->toArray();
		$data['msg'] = self::with('userhas')
					->where([
							'form_id' => $opend,
							'status' => 1,
							'to_read' => 0
						])
					->field('re_id,c_time,form_id,postscript,to_status')
					->select()->toArray();
		return $data;
	}
}