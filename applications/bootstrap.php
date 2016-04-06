<?php
// 设置默认时区
date_default_timezone_set('Asia/Shanghai');

// 在开发环境下，开启 notice
if ( isset($_SERVER['BOOTPHP_ENV']) )
{
	error_reporting(E_ALL);
}

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
\Bootphp\Route::set('default', '(<application>(/<controller>(/<id>)(/<action>)))', ['id' => '\d+'])
	->defaults(array(
		'application' => 'index',
		'controller' => 'index',
		'action' => 'index',
	));
