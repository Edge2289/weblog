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
					'url' => '/index/comment/commentList'
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
					'url' => '/index/user/userList'
				],
				'navlist' => [
					'title' => '文章分类',
					'icon' => '&#xe6a7;',
					'url' => '/index/article/cate'
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
				// 'navlist' => [
				// 	'title' => '文章分类',
				// 	'icon' => '&#xe6a7;',
				// 	'url' => '/index/article/cate'
				// ],
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
];