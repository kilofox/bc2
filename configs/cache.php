<?php
defined('APP_PATH') || exit('Access Denied.');
return array(
	/** 86400 = 1 天
	 * 您可以随时从管理后台手动清除缓存。
	 */
	'apc' => array(
		'driver' => 'apc',
		'default_expire' => 86400
	),
	'file' => array(
		'driver' => 'file',
		'cache_dir' => APP_PATH . '/cache',
		'default_expire' => 86400,
		'ignore_on_delete' => array(
			'.gitignore',
			'.git',
			'.svn'
		)
	),
	'memcache' => array(
		'driver' => 'memcache',
		'default_expire' => 86400,
		'compression' => false, // 使用 Zlib 压缩（用整数可能会有问题）
		'servers' => array(
			array(
				'host' => 'localhost', // Memcache 服务器
				'port' => 11211, // Memcache 端口号
				'persistent' => false, // 持久连接
				'weight' => 1,
				'timeout' => 1,
				'retry_interval' => 15,
				'status' => true,
			),
		),
		'instant_death' => true // 服务器首次失效后立即脱机（不做重试）
	)
);
