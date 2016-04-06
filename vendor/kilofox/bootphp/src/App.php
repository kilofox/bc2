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
		set_exception_handler(['Bootphp\Exception\ExceptionHandler', 'handler']);

		// Enable error handling, converts all PHP errors to exceptions.
		set_error_handler(['Bootphp\App', 'errorHandler']);

		// 如果指定了模板配置
		if ( isset($values['template']) )
		{
			//View::config($values['template']);
		}
	}
	/**
	 * Run app.
	 *
	 * @return	void
	 */
	public function run()
	{
		$request = new \Bootphp\Request();
		$response = new \Bootphp\Response();

		$requestPath = $request->url();
		$params = $request->process($request);
		$requestUri = $request->uri();

		$application = $params['application'];
		$controller = $params['controller'];
		$action = $params['action'];

		$controllerClass = 'App\\' . $application . '\\controllers\\' . ucfirst($controller) . 'Controller';
		if ( !class_exists($controllerClass) )
		{
			throw new ExceptionHandler('The requested URL ' . $requestUri . ' was not found on this server.', 404);
		}

		// Load the controller using reflection
		$class = new \ReflectionClass($controllerClass);
		if ( $class->isAbstract() )
		{
			throw new ExceptionHandler('Cannot create instances of abstract ' . $controllerClass . '.', 404);
		}

		// Create a new instance of the controller
		$ctrler = $class->newInstance($request, $response);

		$ctrler->application = $application;
		$ctrler->controller = $controller;
		$ctrler->action = $action;
		$ctrler->baseUrl = '/' . trim(mb_substr($requestUri, 0, mb_strrpos($requestUri, $requestPath)), '/');

		// Execute the "before action" method
		$class->getMethod('before')->invoke($ctrler);

		// If the action doesn't exist, it's a 404
		if ( !$class->hasMethod($action . 'Action') )
		{
			throw new ExceptionHandler('The requested URL ' . $requestUri . ' was not found on this server.', 404);
		}

		// Execute the action itself
		$class->getMethod($action . 'Action')->invoke($ctrler);

		// Execute the "after action" method
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