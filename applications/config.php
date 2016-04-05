<?php
// 传递给 Bootphp/App 主实例的配置
return array(
	'env' => getenv('BOOTPHP_ENV') ? getenv('BOOTPHP_ENV') : 'development',
	'context.cfg' => array(
		'path' =>  __DIR__ . '/contexts/'
	),
	'template.cfg' => array(
		'path' => __DIR__ . '/templates/',
		'path_layouts' => __DIR__ . '/templates/layout/',
		'auto_layout' => 'application'
	)
);
