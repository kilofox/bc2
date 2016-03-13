<?php

namespace Bootphp;
/**
 * 路由用来根据 URI 的控制器后面的部分确定动作、参数以及参数的值。
 *
 * 每个 <键> 将使用默认正则表达式模式转换成正则表达式。
 *
 *   // 这个路由将确定动作 action 和参数 id 及其值：
 *   Route::set(array('<id>' => 'action'));
 *
 * @package BootPHP
 * @category Base
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class Route
{
	// 哪些可以作为路径分段的值
	const REGEX_SEGMENT = '[^/.,;?\n]++';
	// 正则表达式中的哪些字符必须被转义
	const REGEX_ESCAPE = '[.\\+*?[^\\]${}=!|]';
	/**
	 * @var	array 要测试的路由数组
	 */
	protected static $_routes = [];
	/**
	 * 存储要测试的路由数组，并返回它。
	 *
	 *   Route::set(array('<id>' => 'action'));
	 *
	 * @param array	 所有要测试的路由数组
	 * @return	array
	 */
	public function set(array $routes)
	{
		return self::$_routes = $routes;
	}
	/**
	 * 返回编译的路由正则表达式。将键翻译成正确的 PCRE 正则表达式。
	 *
	 * 	$compiled = Route::compile('<id>');
	 *
	 * @return	string
	 * @uses Route::REGEX_ESCAPE
	 * @uses Route::REGEX_SEGMENT
	 */
	public static function compile($regulation)
	{
		if ( !is_string($regulation) )
			return;
		// 转义除 < > 以外的所有 preg_quote 将要转义的东西
		$expression = preg_replace('#' . self::REGEX_ESCAPE . '#', '\\\\$0', $regulation);
		// 为键插入默认正则
		$expression = str_replace(array('<', '>'), array('(?P<', '>' . self::REGEX_SEGMENT . ')'), $expression);
		return '#^' . $expression . '$#uD';
	}
	/**
	 * 测试所有路由，查找与所给路径分段匹配的路由。返回匹配的路由指定的动作与参数的数组。
	 *
	 *   // 路由数组：array('<id>' => 'profile')
	 *   // 路径分段：'10'
	 *   // 动作与参数：$action = 'profile', $params = array('id' => '10')
	 *   list($action, $params) = $route->matches('10');
	 *
	 * @param	string	要匹配的路径分段（控制器后面的部分）
	 * @return	array	动作与参数
	 */
	public function matches($path)
	{
		$action = isset($path[0]) ? $path[0] : 'index';
		$path = implode('/', $path);
		// 匹配的参数
		$params = [];
		foreach( self::$_routes as $key => $value )
		{
			$regex = self::compile($key);
			if ( preg_match($regex, $path, $matches) )
			{
				foreach( $matches as $key => $val )
				{
					// 跳过所有未命名的键
					if ( is_int($key) )
						continue;
					// 为所有匹配的键赋值
					$params[$key] = $val;
				}
				// 匹配的动作
				$action = $value;
				break;
			}
		}
		return array($action, $params);
	}
}