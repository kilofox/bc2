<?php

namespace Bootphp;
/**
 * 消息日志记录。
 *
 * @package BootPHP
 * @category 日志
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class Log
{
	// 日志消息级别
	const EMERGENCY = LOG_EMERG; // 0
	const ALERT = LOG_ALERT; // 1
	const CRITICAL = LOG_CRIT; // 2
	const ERROR = LOG_ERR; // 3
	const WARNING = LOG_WARNING; // 4
	const NOTICE = LOG_NOTICE; // 5
	const INFO = LOG_INFO; // 6
	const DEBUG = LOG_DEBUG; // 7
	const STRACE = 8;
	/**
	 * @var string 日志记录的时间格式
	 */
	private static $timestamp = 'Y-m-d H:i:s';
	/**
	 * @var string 日志记录的时区
	 */
	private static $timezone;
	/**
	 * @var Log 单例容器
	 */
	private static $instance;
	/**
	 * @var string 放置日志文件的目录
	 */
	private $directory;
	/**
	 * @var array 添加的消息列表
	 */
	private $messages = [];
	/**
	 * 日志级别数字转字符串查找表
	 * @var array
	 */
	private $logLevels = array(
		LOG_EMERG => 'EMERGENCY',
		LOG_ALERT => 'ALERT',
		LOG_CRIT => 'CRITICAL',
		LOG_ERR => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE => 'NOTICE',
		LOG_INFO => 'INFO',
		LOG_DEBUG => 'DEBUG',
		8 => 'STRACE'
	);
	/**
	 * 获得类的单例，创建一个新的文件记录器，检查目录是否存在并且可写。在关闭时开启日志写入。
	 *
	 *   $log = Log::instance(APP_PATH . '/logs');
	 *
	 * @param	string	$directory 日志目录
	 * @return	Log
	 */
	public static function instance($directory)
	{
		if ( self::$instance === NULL )
		{
			// 创建一个新的实例
			self::$instance = new Log;
			// 关闭时写下日志
			register_shutdown_function(array(self::$instance, 'write'));
		}
		if ( !is_dir($directory) || !is_writable($directory) )
			exit('目录 ' . Debug::path($directory) . ' 必须是可写的');
		// 确定目录路径
		self::$instance->directory = realpath($directory) . DIRECTORY_SEPARATOR;
		return self::$instance;
	}
	/**
	 * 将消息添加到日志中
	 *
	 *   $log->add(self::ERROR, '无法找到用户：' . $username);
	 *
	 * @param	string	消息级别
	 * @param	string	消息主体
	 * @return	void
	 */
	public function add($level, $message)
	{
		// 创建一个新的消息
		$this->messages[] = array(
			'time' => Date::formattedTime('now', self::$timestamp, self::$timezone),
			'level' => $level,
			'body' => $message,
		);
	}
	/**
	 * 写入所有消息
	 *
	 *   $log->write();
	 *
	 * @return	void
	 */
	public function write()
	{
		if ( empty($this->messages) )
		{
			// 没什么要写的
			return;
		}
		$messages = $this->messages;
		// 重置消息数组
		$this->messages = [];
		// 设置年份目录名
		$directory = $this->directory . date('Y');
		if ( !is_dir($directory) )
		{
			// 创建年份目录
			mkdir($directory, 02777);
			// 设置权限
			chmod($directory, 02777);
		}
		// 将月份添加到目录中
		$directory .= DIRECTORY_SEPARATOR . date('m');
		if ( !is_dir($directory) )
		{
			// 创建月份目录
			mkdir($directory, 02777);
			// 设置权限
			chmod($directory, 02777);
		}
		// 设置日志文件名
		$filename = $directory . DIRECTORY_SEPARATOR . date('d') . '.php';
		if ( !file_exists($filename) )
		{
			// 创建日志文件
			file_put_contents($filename, '<?php defined(\'APP_PATH\') || exit(\'Access Denied.\'); ?>' . PHP_EOL);
			// 允许任何人写日志文件
			chmod($filename, 0666);
		}
		foreach( $messages as $message )
		{
			// 将各条消息写入日志文件
			// 格式：时间 --- 级别: 主体
			file_put_contents($filename, PHP_EOL . $message['time'] . ' --- ' . $this->logLevels[$message['level']] . ': ' . $message['body'], FILE_APPEND);
		}
	}
}