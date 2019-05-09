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


/**
     * [getIPInfo 获取IP信息]
     * @return [type] [description]
     */
    function getIPInfo(){
        if (getenv("HTTP_CLIENT_IP")){
            return getenv("HTTP_CLIENT_IP");
        }else if(getenv("HTTP_X_FORWARDED_FOR")){
            return getenv("HTTP_X_FORWARDED_FOR");
        }else if(getenv("REMOTE_ADDR")){ 
            return getenv("REMOTE_ADDR"); 
        }else if($_SERVER["REMOTE_ADDR"]) {
            return $_SERVER["REMOTE_ADDR"];
        }
    }


/**
     * [getCity 根据IP获取城市之类信息]
     * @return [type] [description]
     */
    function getCity($ip = ''){
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ipInfoData = json_decode(UrlCurl($url,$ip),true);
        if ($ipInfoData['code'] == 404) {
            $ipInfoData['msg'] = "暂未获取数据";
        }
        $ipInfoData['info'] = $_SERVER['HTTP_USER_AGENT'];
        return $ipInfoData;
    }

    /**
     * [UrlCurl 获取IP地址curl]
     * @param string $url [description]
     * @param [type] $ip  [description]
     */
    function UrlCurl($url = '', $ip){
        if ($ip == '') {
            return json_encode(["code" => 404,
                    "data" => []
                ]);
        }
        if ($ip == '127.0.0.1') {
            return json_encode(["code" => 0,
                    "data" => [
                        "ip" => "127.0.0.1",   // ip
                        "country" => "XX",     // 国家
                        "area" => "",
                        "region" => "XX",       // 省份
                        "city" => "内网IP",       // 城市
                        "county" => "内网IP",
                        "isp" => "内网IP",        // 运营商
                        "country_id" => "xx",     // 国家代号 CN 中国
                        "area_id" => "",
                        "region_id" => "xx",        // 区号
                        "city_id" => "local",
                        "county_id" => "local",
                        "isp_id" => "local"
                    ]
                ]);
        }
        // 1. 初始化
         $ch = curl_init();
         // 2. 设置选项，包括URL
         curl_setopt($ch,CURLOPT_URL,$url);
         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt($ch,CURLOPT_HEADER,0);
         // 3. 执行并获取HTML文档内容
         $output = curl_exec($ch);
         // 4. 释放curl句柄
         curl_close($ch);
         return $output;
         // if($output === FALSE ){
         // echo "CURL Error:".curl_error($ch);
         // }
    }



    /**
     * [getSourceCli 解析来源Url]
     * @param  [type] $referer [来源url]
     * @return [type]          [
     *         '参数' -> '平台的来源'
     * ]
     */
    function getSourceCli($referer){
        if (empty($referer)) {
            return ["keyWord" => "","fromtype" => "手动打开"];
        }
         // 解析URL数组
        $list = parse_url($referer);
        $reData = convertUrlQuery($list['query']);
        $keyWord = '';
        $fromtype = 'direct';

        // 百度的来源
        if (!empty($reData['word'])) {
            $keyWord = $reData['word'];
            $fromtype = 'baidu';
        }else if (!empty($reData['wd'])){
            $keyWord = $reData['wd'];
            $fromtype = 'baidu';
        }

        // 搜狗的来源 sogou.com
        if (strstr($referer, ".sogou.com")) {
            $keyWord = $reData['keyword'];
            $fromtype = 'sogou';
            if ($keyWord == '') {
                $keyWord = $reData['query'];
            }
        }

        // 神马 sm.cn
        if (strstr($referer, ".sm.cn")) {
            $keyWord = $reData['q'];
            $fromtype = 'sm';
        }

        // 360  m.so.com
        if (strstr($referer, "m.so.com")) {
            $keyWord = $reData['q'];
            $fromtype = 'so';
        }

        // 谷歌 google
        if (strstr($referer, "google.com")) {
            $keyWord = $reData['q'];
            $fromtype = 'google';
            if ($keyWord == '') {
                $keyWord = $reData['oq'];
            }
        }
        return ["keyWord" => $keyWord,"fromtype" => $fromtype];
    }

    function convertUrlQuery($query)
        { 
            $queryParts = explode('&', $query); 
            
            $params = array(); 
            foreach ($queryParts as $param) 
            { 
                $item = explode('=', $param); 
                $params[$item[0]] = $item[1]; 
            } 
            
            return $params; 
        }

// 获取操作系统
function getOS()
{
  $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

  if(strpos($agent, 'windows nt')) {
  $platform = 'windows';
  } elseif(strpos($agent, 'macintosh')) {
  $platform = 'mac';
  } elseif(strpos($agent, 'ipod')) {
  $platform = 'ipod';
  } elseif(strpos($agent, 'ipad')) {
  $platform = 'ipad';
  } elseif(strpos($agent, 'iphone')) {
  $platform = 'iphone';
  } elseif (strpos($agent, 'android')) {
  $platform = 'android';
  } elseif(strpos($agent, 'unix')) {
  $platform = 'unix';
  } elseif(strpos($agent, 'linux')) {
  $platform = 'linux';
  } else {
  $platform = 'other';
  }

  return $platform;
}     

function dailStatisticsDiskRoom(){

  $fd = popen('df -lh | grep -E "^(/)"', "r");
  $rs = fread($fd, 1024);
  pclose($fd);
  $rs = preg_replace("/\s{2,}/", ' ', $rs);
  $hd = explode(" ", $rs);
  return $hd;
}