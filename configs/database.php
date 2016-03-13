<?php
defined('APP_PATH') || exit('Access Denied.');
return array(
	'default' => array(
		'type' => 'mysql',
		'connection' => array(
			/**
			 * string	dsn			数据源名
			 * string	username	数据库用户名
			 * string	password	数据库密码
			 * boolean	persistent	是否使用持久连接
			 */
			'dsn' => 'mysql:host=localhost;dbname=bootcms2',
			'username' => 'root',
			'password' => 'root',
			'persistent' => false
		),
		'table_prefix' => 'bc_',
		'charset' => 'utf8',
		'caching' => false,
		'profiling' => true
	),
	'default2' => array(
		'type' => 'mysql',
		'connection' => array(
			/**
			 * string	dsn			数据源名
			 * string	username	数据库用户名
			 * string	password	数据库密码
			 * boolean	persistent	是否使用持久连接
			 */
			'dsn' => 'mysql:host=localhost;dbname=kilofoxx_bootcms',
			'username' => 'kilofoxx_bootcms',
			'password' => 'bootcms2014',
			'persistent' => false
		),
		'table_prefix' => 'bc_',
		'charset' => 'utf8',
		'caching' => false,
		'profiling' => false
	)
);
