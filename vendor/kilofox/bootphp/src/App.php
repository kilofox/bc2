<?php

namespace Bootphp;
use Bootphp\Exception\ExceptionHandler;
/**
 * App class.
 *
 * @package	Bootphp
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class App
{
	// Release version
	const VERSION = '2.0.0';
	/**
	 * New App instance
	 *
	 * @param array $values 传递给容器的配置与对象数组
	 */
	public function __construct(array $values = [])
	{
		// Enable exception handling, adds stack traces and error source.
		set_exception_handler(array('Bootphp\Exception\ExceptionHandler', 'handler'));
		// Enable error handling, converts all PHP errors to exceptions.
		set_error_handler(array('Bootphp\App', 'errorHandler'));
		// 如果指定了模板配置
		if ( isset($values['template']) )
		{
			View::config($values['template']);
		}
	}
	/**
	 * Run app with given REQUEST_METHOD and REQUEST_URI
	 *
	 * @return	void
	 */
	public function run(Request $request)
	{
		$requestPath = $request->url();
		$params = $request->process($requestPath);
		print_r($params);
		// 分割路径，去掉开头和结尾的斜杠
		$paths = explode('/', $requestPath);
		$application = $params['application'];
		$controller = $params['controller'];
		$action = $params['action'];
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
		$ctrler->action = $action;
		$ctrler->paths = $paths;
		$requestUri = $request->uri();
		$ctrler->baseUrl = '/' . trim(mb_substr($requestUri, 0, mb_strrpos($requestUri, $requestPath)), '/');
		// 执行 before 方法
		$class->getMethod('before')->invoke($ctrler);
		//$action = $ctrler->action;
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
	 * PHP error handler, converts all errors into ErrorExceptions. This handler
	 * respects error_reporting settings.
	 *
	 * @throws	ErrorException
	 * @return	true
	 */
	public static function errorHandler($code, $error, $file = NULL, $line = NULL)
	{
		if ( error_reporting() & $code )
		{
			// This error is not suppressed by current error reporting settings
			// Convert the error into an ErrorException
			throw new \ErrorException($error, $code, 0, $file, $line);
		}

		// Do not execute the PHP error handler
		return true;
	}
}