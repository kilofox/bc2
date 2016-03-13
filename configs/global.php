<?php
// 传递给 Bootphp/App 主实例的配置
return array(
	'env' => getenv('BOOTPHP_ENV') ? getenv('BOOTPHP_ENV') : 'development',
	'template' => array(
		'path' => APP_PATH . '/templates/',
		'path_layouts' => APP_PATH . '/templates/layout/',
		'auto_layout' => 'layout',
		'default' => 'metro'
	)
);
