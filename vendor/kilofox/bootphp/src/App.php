<?php

namespace Bootphp;
use Bootphp\Exception\ExceptionHandler;
/**
 * 应用类
 *
 * @package Bootphp
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class App
{
	// 发行版本
	const VERSION = '2.0.0';
	/**
	 * 实例化应用
	 *
	 * @param array $values 传递给容器的配置与对象数组
	 */
	public function __construct(array $values = [])
	{
		// 启用异常处理，添加堆栈跟踪和错误源。
		set_exception_handler(array('Bootphp\Exception\ExceptionHandler', 'handler'));
		// 启用 Bootphp 错误处理，将所有 PHP 错误转换为异常。
		set_error_handler(array('Bootphp\App', 'errorHandler'));
		// 如果指定了模板配置
		if ( isset($values['template']) )
		{
			View::config($values['template']);
		}
	}
	/**
	 * 用给定的请求方式和请求URI运行应用
	 *
	 * @return	void
	 */
	public function run(Request $request)
	{
		$requestPath = $request->url();
		// 分割路径，去掉开头和结尾的斜杠
		$paths = explode('/', $requestPath);
		$application = $paths[0] ? $paths[0] : 'index';
		$controller = isset($paths[1]) && $paths[1] ? $paths[1] : 'index';
		//$action = isset($paths[2]) && $paths[2] ? $paths[2] : 'index';
		$controllerClass = 'App\\' . $application . '\\controllers\\' . ucfirst($controller) . 'Controller';
		if ( !class_exists($controllerClass) )
		{
			throw new ExceptionHandler('类 ' . $controllerClass . ' 在服务器上未找到。');
		}
		// 使用反射加载控制器
		$class = new \ReflectionClass($controllerClass);
		if ( $class->isAbstract() )
		{
			throw new ExceptionHandler('不能用抽象类 ' . $controllerClass . ' 创建实例。');
		}
		$response = new \Bootphp\Response();
		// 创建一个新的控制器实例
		$ctrler = $class->newInstance($request, $response);
		$ctrler->application = $application;
		$ctrler->controller = $controller;
		//$ctrler->action = $action;
		$ctrler->paths = $paths;
		$requestUri = $request->uri();
		$ctrler->baseUrl = '/' . trim(mb_substr($requestUri, 0, mb_strrpos($requestUri, $requestPath)), '/');
		// 执行 before 方法
		$class->getMethod('before')->invoke($ctrler);
		$action = $ctrler->action;
		// 如果动作不存在，那就是 404
		if ( !$class->hasMethod($action . 'Action') )
		{
			throw new ExceptionHandler('请求的URL ' . $request->uri() . ' 在服务器上未找到。', 404);
		}
		$method = $class->getMethod($action . 'Action');
		$method->invoke($ctrler);
		// 执行 after 方法
		$class->getMethod('after')->invoke($ctrler);
	}
	/**
	 * PHP错误处理器，将错误转换为 ErrorException。这个处理器关联 error_reporting 设置。
	 *
	 * @throws	ErrorException
	 * @return	true
	 */
	public static function errorHandler($code, $error, $file = NULL, $line = NULL)
	{
		if ( error_reporting() & $code )
		{
			// 将错误转换成一个 ErrorException。
			throw new \ErrorException($error, $code, 0, $file, $line);
		}
		return true;
	}
}