<?php
namespace app\index\controller;

use think\Request;
use app\index\common\Base;

/**
*  上传类
*/
class Upload extends Base
{

	protected $requset ;

	public function __construct(){

		parent::__construct();

		$dir = iconv('UTF-8', "GBK", ROOT_PATH . 'public' . DS . 'static' . DS . 'img' . DS . 'uploads'. DS. date("Ymd"));
		$dirbanner = iconv('UTF-8', "GBK", ROOT_PATH . 'public' . DS . 'static' . DS . 'img' . DS . 'banner'. DS. date("Ymd"));
		   if (!file_exists($dir)) {
		   		mkdir($dir, 0777, true);
		   }
		   if (!file_exists($dirbanner)) {
		   		mkdir($dirbanner, 0777, true);
		   }
		 /** 检查目录是否可写 */
		   if (!@is_writable(ROOT_PATH . 'public' . DS . 'static' . DS . 'img' . DS . 'uploads')) {
		   	return [
						'code' => 0,
						'msg' => "目录不可写",
						'data' => [],
				];
		   }

		$this->requset = Request::instance();
	}	

	public function articleimg(){
		$data = $this->requset->file('images');
		$filelj = 'static' . DS . 'img' . DS . 'uploads'. DS. date("Ymd");

		$uploadedfile = $_FILES['file']['tmp_name'];  // 获取文件的缓存

		list($width,$height,$attr) = getimagesize($uploadedfile); // 获取文件的宽高
		if ($attr == 2) {
			$src = imagecreatefromjpeg($uploadedfile);  // 获取文件
		}else if ($attr == 3){
			$src = imagecreatefrompng($uploadedfile);  // 获取文件
		}
		$newwidths = 500; // 设定需要设置的宽
		$newheights = 400;// 设定需要设置的高
		$tmp=imagecreatetruecolor($newwidths,$newheights);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidths,$newheights,$width,$height);
		$filename = $filelj. DS. md5(time()). '.' .'jpg';
		imagejpeg($tmp,ROOT_PATH . 'public' . DS . $filename,100);
		imagedestroy($src);
		imagedestroy($tmp);
		$filename = str_replace("\\","/",DS .$filename);
        if(file_exists(ROOT_PATH . 'public' . DS . $filename)){
            // 成功上传后 获取上传信息
			return [
					'code' => 1,
					'msg' => "上传成功",
					'data' => [
						'src' => $filename,
					],
			];

        }else{
			return [
					'code' => 0,
					'msg' => "上传失败",
					'data' => [],
			];
        }    
	}

	/**
	 * [bannerimg banner 图片上传]
	 * @return [type] [description]
	 */
	public function bannerimg(){
		$data = $this->requset->file('images');
		$filelj = 'static' . DS . 'img' . DS . 'banner'. DS. date("Ymd");

		$uploadedfile = $_FILES['file']['tmp_name'];  // 获取文件的缓存
		list($width,$height,$attr) = getimagesize($uploadedfile); // 获取文件的宽高
		if ($attr == 2) {
			$src = imagecreatefromjpeg($uploadedfile);  // 获取文件
		}else if ($attr == 3){
			$src = imagecreatefrompng($uploadedfile);  // 获取文件
		}
		$newwidths = 1200; // 设定需要设置的宽
		$newheights = 842;// 设定需要设置的高
		$tmp=imagecreatetruecolor($newwidths,$newheights);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidths,$newheights,$width,$height);
		$filename = $filelj. DS. md5(time()). '.' .'jpg';
		imagejpeg($tmp,ROOT_PATH . 'public' . DS . $filename,100);
		imagedestroy($src);
		imagedestroy($tmp);
		$filename = str_replace("\\","/",DS .$filename);
        if(file_exists(ROOT_PATH . 'public' . DS . $filename)){
            // 成功上传后 获取上传信息
			return [
					'code' => 1,
					'msg' => "上传成功",
					'data' => [
						'src' => $filename,
					],
			];

        }else{
			return [
					'code' => 0,
					'msg' => "上传失败",
					'data' => [],
			];
        }  
	}
}






