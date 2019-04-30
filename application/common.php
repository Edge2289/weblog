<?php

// 应用公共文件

/**
 * [DataReturn 返回状态数据]
 * 
 * @param    [string]       $msg  [提示信息]
 * @param    [int]          $code [状态码]
 * @param    [mixed]        $data [数据]
 * @return   [json]               [json数据]
 */
function DataReturn($code = 0 ,$msg = '' ,$data = []){

	// $code 为0 success 为-1 error 
	if ($code == 0) {
		if (empty($msg)) {
			$msg = "操作成功！";
		}
	}else{
		if (empty($msg)) {
			$msg = "操作失败！";
		}
	}

	$result = array('code'=>$code,'msg'=>$msg,'data'=>$data);
	return json_encode($result);
}

	
function pwmd5($pass , $halt = "xiaoxiaoHalt"){

	return md5(md5($pass).$halt);
}