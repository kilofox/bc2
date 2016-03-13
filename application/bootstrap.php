<?php
// 设置默认时区
date_default_timezone_set('Asia/Shanghai');

// 在开发环境下，开启 notice
if ( isset($_SERVER['BOOTPHP_ENV']) )
{
	error_reporting(E_ALL);
}