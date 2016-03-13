<?php

namespace Bootphp\Exception;
use Bootphp\BootPHP;
use Bootphp\Log;
use Bootphp\Request;
use Bootphp\Response;
/**
 * BootPHP 异常类。
 *
 * @package BootPHP
 * @category 异常
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class ExceptionHandler extends \Exception
{
	/**
	 * @var	array PHP错误代码 => 人类可读的名称
	 */
	public static $phpErrors = array(
		E_ERROR => 'Fatal Error',
		E_USER_ERROR => 'User Error',
		E_PARSE => 'Parse Error',
		E_WARNING => 'Warning',
		E_USER_WARNING => 'User Warning',
		E_STRICT => 'Strict',
		E_NOTICE => 'Notice',
		E_RECOVERABLE_ERROR => 'Recoverable Error',
		E_DEPRECATED => 'Deprecated'
	);
	/**
	 * 创建一个新的翻译了的异常。
	 *
	 * 	 throw new ExceptionHandler('出现了一些可怕的错误');
	 *
	 * @param	string	错误消息
	 * @param integer|string 异常代码
	 * @return	void
	 */
	public function __construct($message, $code = 0)
	{
		// 向父类传递消息和整型代码
		parent::__construct($message, (int)$code);
		// 保存未修改过的代码
		$this->code = $code;
	}
	/**
	 * 内联异常处理器，显示错误消息、异常源码和错误的堆栈跟踪。
	 *
	 * @uses ExceptionHandler::text
	 * @param object 异常对象
	 * @return	boolean
	 */
	public static function handler(\Exception $e)
	{
		try
		{
			// 记录异常
			self::log($e);
			// 生成响应
			$response = self::response($e);
			// 向浏览器发送响应
			$response->send();
			exit(1);
		}
		catch( Exception $e )
		{
			// 事情进展得相当糟糕，我们现在别无选择，直接输出吧。
			// 清理输出缓存
			ob_get_level() && ob_clean();
			// 设置状态码为 500，Content-Type 为 text/plain
			header('Content-Type: text/plain; charset=UTF-8', true, 500);
			echo self::text($e);
			exit(1);
		}
	}
	/**
	 * 记录一个异常。
	 *
	 * @uses ExceptionHandler::text
	 * @param Exception $e
	 * @param int $level
	 * @return	void
	 */
	public static function log(\Exception $e, $level = Log::EMERGENCY)
	{
		$log = Log::instance(APP_PATH . '/logs');
		// 创建一个异常的文本
		$error = self::text($e);
		// 将这个异常添加到日志中
		$log->add($level, $error);
		// 写入日志
		$log->write();
	}
	/**
	 * 取得代表异常的单行文本：
	 *
	 * Error [ Code ]: Message ~ File [ Line ]
	 *
	 * @param object Exception
	 * @return	string
	 */
	public static function text(\Exception $e)
	{
		return get_class($e) . ' [ ' . $e->getCode() . ' ]: ' . strip_tags($e->getMessage()) . ' ~ ' . \Bootphp\Debug::path($e->getFile()) . ' [ ' . $e->getLine() . ' ]';
	}
	/**
	 * 取得一个代表异常的 Response 对象
	 *
	 * @uses ExceptionHandler::text
	 * @param Exception $e
	 * @return	Response
	 */
	public static function response(\Exception $e)
	{
		try
		{
			// 获取异常信息
			$class = get_class($e);
			$code = $e->getCode();
			$message = $e->getMessage();
			$file = $e->getFile();
			$line = $e->getLine();
			$trace = $e->getTrace();

			if ( $e instanceof \ErrorException )
			{
				if ( isset(self::$phpErrors[$code]) )
				{
					// 使用人类可读的错误名称
					$code = self::$phpErrors[$code];
				}
			}

			// 初始化错误视图
			$view = new \Bootphp\View();
			$view->path(__DIR__ . '/Views/');
			$view->file('error');
			$view->layout(false);
			$view->set(get_defined_vars());

			// 准备响应对象
			$response = new Response();

			// 设置响应状态
			$response->status($response->statusText($code) ? $code : 500);

			// 设置响应主体
			$response->content($view->render());
		}
		catch( Exception $e )
		{
			// 事情进展得很糟糕，我们来生成一个简单的响应对象，使其可控。
			$response = new Response();
			$response->status(500);
			$response->header('Content-Type', 'text/plain');
			$response->content(self::text($e));
		}
		return $response;
	}
}