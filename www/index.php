<?php
###开发可打开调试模式，上线时一定要关闭
define('DEV_DEBUG', true);
define('DEBUG_LOG', true);
###一定必须要定义根目录
define('ROOT_DIR', dirname(__DIR__));
date_default_timezone_set('PRC');
require(ROOT_DIR . '/vendor/autoload.php');

//use beacon\Route as Route; //无工具路由
use libs\zero\Boot as Route; //有工具路由

Route::register('home');
Route::register('admin');
Route::register('service');
Route::register('tool');
Route::run();
