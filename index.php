<?php
// 设置内部字符编码；PHP 5.6 及以上版本不需要开启
mb_internal_encoding('UTF-8');

// 定义应用目录
define('APP_PATH', __DIR__ . '/application');
// 定义应用的开始时间，用于性能分析。
defined('START_TIME') || define('START_TIME', microtime(true));
// 定义应用开始时的内存使用，用于性能分析。
defined('START_MEMORY') || define('START_MEMORY', memory_get_usage());

// 自动加载器
require __DIR__ . '/vendor/autoload.php';
AutoloaderInit::getLoader();

// 引导应用程序
require APP_PATH . '/bootstrap.php';

// 请求
$request = new \Bootphp\Request();

// CLI 场景
if ( $request->isCli() )
{
	// CLI 尚未实现
	exit;
}

// Bootphp 应用
$app = new \Bootphp\App(require __DIR__ . '/configs/global.php');
// 执行请求
$app->run($request);
