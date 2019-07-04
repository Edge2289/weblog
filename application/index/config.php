<?php

return [
	'nav' => [
		'book' => [
			'title' => '文章管理',
			'icon' => '&#xe6a2;',
			'son' => [
				'booklist' => [
					'title' => '文章列表',
					'icon' => '&#xe6a7;',
					'url' => '/index/article/articleList'
				],
				'navlist' => [
					'title' => '文章分类',
					'icon' => '&#xe6a7;',
					'url' => '/index/article/cate'
				],
			],
		],

		'comment' => [
			'title'=>'评论管理',
			'icon' => '&#xe6b2;',
			'son' => [
				'commentlist' => [
					'title' => '评论列表',
					'icon' => '&#xe6a7;',
					'url' => '/index/comment/index'
				],
			],
		],

		'banner' => [
			'title'=>'导航轮播',
			'icon' => '&#xe6a8;',
			'son' => [
				'commentlist' => [
					'title' => '轮播图',
					'icon' => '&#xe6a7;',
					'url' => '/index/banner/index'
				],
			],
		],

		'user' => [
			'title'=>'用户管理',
			'icon' => '&#xe6b8;',
			'son' => [
				'userlist' => [
					'title' => '用户列表',
					'icon' => '&#xe6a7;',
					'url' => '/index/user/index'
				],
			],
		],

		'admin' => [
			'title'=>'管理员管理',
			'icon' => '&#xe726;',
			'son' => [
				'adminlist' => [
					'title' => '管理员列表',
					'icon' => '&#xe6a7;',
					'url' => '/index/admin/index'
				],
			],
		],

		'visitor' => [
			'title'=>'访客数据',
			'icon' => '&#xe726;',
			'son' => [
				'visitorlist' => [
					'title' => '用户来源列表',
					'icon' => '&#xe6a7;',
					'url' => '/index/visitor/source'
				],
				'articlelist' => [
					'title' => '访问文章统计',
					'icon' => '&#xe6a7;',
					'url' => '/index/visitor/articletj'
				],
				'loglist' => [
					'title' => '用户登录数据',
					'icon' => '&#xe6a7;',
					'url' => '/index/visitor/userloglist'
				],
			],

		],

		'system' => [
			'title'=>'系统设置',
			'icon' => '&#xe6ae;',
			'son' => [
				'systemtlist' => [
					'title' => '系统参数',
					'icon' => '&#xe6a7;',
					'url' => '/index/system/index'
				],
			],
		],
	],

	'url' => 'bogo.uikiss.cn',

<<<<<<< HEAD
    'qq' => [
=======

	'qq' => [
>>>>>>> f328e4d0ac477da8fcd3b45c9c84ca358cb7b41b
        'appid' => '101571601',
        'appkey' => '65beee4cad70abfc9fb337123fe28f7a',
        'callback' => 'http://bogo.uikiss.cn/index/Callback/qqCallback',
        'scope' => 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr',
        'errorReport' => true
    ],
<<<<<<< HEAD
];
=======

];
>>>>>>> f328e4d0ac477da8fcd3b45c9c84ca358cb7b41b
