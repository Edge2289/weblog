<?php

use think\Db;
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

/**
 * 用递归获取子类信息
 * $tree 所有分类
 * $rootId 父级id
 * $result 分好类的数组
*/

function commentTree($tree, $rootId = 0) { 

  $return = array(); 
  foreach($tree as $leaf) { 
    if($leaf['target_id'] == $rootId) { 
      foreach($tree as $subleaf) { 
        if($subleaf['target_id'] == $leaf['comment_id']) { 
          $leaf['children'] = commentTree($tree, $leaf['comment_id']); 
          break; 
        } 
      } 
      $return[] = $leaf; 
    } 
  } 
  return $return; 
} 

/**
 *  遍历树形做回二维数组
 */
function commentBig($tree){

	$return = array(); 
	  foreach($tree as $k1 => $leaf) { 
	    if($leaf['target_id'] == 0) { 
	    	unset($tree[$k1]);
	    	// $leaf['comment_val'] = htmlentities($leaf['comment_val']);
	    	$return[] = $leaf; 
	    } 
	  }

	  foreach ($return as $key => $value) {
	  	if (!empty($value['children'])) {
	  		$comment_data_time = commentShow($value['children']);
	  		$return[$key]['children'] = maopaoTime($comment_data_time);
	  	}
	  }

    return $return; 
}

// 将子类排序出来
function commentShow($aa){
    $sb = array();
    foreach($aa as $v){
    	$bm = $v;
    	unset($bm['children']);
        $sb[] = $bm;
        if(!empty($v['children'])){
            $sbb = commentShow($v['children']);
            $sb = array_merge($sb,$sbb);
        }
    }
    return $sb;
}

//*
// 冒泡算法 根据时间戳来排序
function maopaoTime($data){
	for ($i=0; $i < count($data); $i++) { 
		if (!empty($data[$i]['target_user_id'])) {
			$data[$i]['target_nick'] = Db('blog_user')->where('user_id',$data[$i]['target_user_id'])->value('user_nick');
		}
		// $data[$i]['comment_val'] = htmlentities($data[$i]['comment_val']);
		$bm = array();
		for ($b = $i; $b < count($data)-1; $b++) { 
			if ($data[$i]['comment_time'] > $data[$b+1]['comment_time']) {
				$bm = $data[$i];
				$data[$i] = $data[$b+1];
				$data[$b+1] = $bm;
			}
		}
	}
	return $data;
}