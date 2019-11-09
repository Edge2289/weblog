<?php

	return [

		// 默認輸出類型
		'default_return_type' => 'json',

		// 默認ajax 數據返回格式 json
		'default_ajax_return' => 'json',

		// 默認json格式返回处理方法
		'default_jsonp_handler' => 'jsonpReturn',
		
		'url' => 'blogapi.uikiss.cn',

		// 验证的参数
		'apiMiddleware' => [
			'app_id' => 'UGX6JHKBhWTPuzZjnzCIB7s8raaY6Kfm', // APPId
			'app_secret' => 'X2fCKYhc4gNH435yTMgyMQVihaexTgevGCPByeNW6vUkHXstAIKwD9Z4XbYByev9'
		],
		// api验证开关
		'apiMiddlewareStatus' => 1,
	];
?>
