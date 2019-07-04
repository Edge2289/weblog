<?php
// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../app/');

require __DIR__ . '/../thinkphp/base.php';// 加载框架基础文件
\think\Route::bind('api');// 绑定当前入口文件到模块
\think\App::route(true);// 路由
\think\App::run()->send();// 执行应用

