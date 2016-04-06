<?php

namespace Bootphp;
/**
 * Message logging with observer-based log writing.
 *
 * [!!] This class does not support extensions, only additional writers.
 *
 * @package	BootPHP
 * @category	Logging
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class Log
{
	// Log message levels
	const EMERGENCY = LOG_EMERG; // 0
	const ALERT = LOG_ALERT; // 1
	const CRITICAL = LOG_CRIT; // 2
	const ERROR = LOG_ERR; // 3
	const WARNING = LOG_WARNING; // 4
	const NOTICE = LOG_NOTICE; // 5
	const INFO = LOG_INFO; // 6
	const DEBUG = LOG_DEBUG; // 7
	/**
	 * @var  Log  Singleton instance container
	 */
	private static $instance;
	/**
	 * @var  array  list of added messages
	 */
	private $messages = [];
	/**
	 * Get the singleton instance of this class and enable writing at shutdown.
	 *
	 *     $log = Log::instance();
	 *
	 * @return  Log
	 */
	public static function instance($directory)
	{
		if ( self::$instance === NULL )
		{
			// Create a new instance
			self::$instance = new Log;

			// Write the logs at shutdown
			register_shutdown_function(array(self::$instance, 'write'));
		}

		return self::$instance;
	}
	/**
	 * Adds a message to the log. Replacement values must be passed in to be
	 * replaced using [strtr](http://php.net/strtr).
	 *
	 *     $log->add(Log::ERROR, 'Could not locate user: :user', array(
	 *         ':user' => $username,
	 *     ));
	 *
	 * @param   string  $level       level of message
	 * @param   string  $message     message body
	 * @return  Log
	 */
	public function add($level, $message)
	{
		// Create a new message
		$this->messages[] = array(
			'time' => time(),
			'level' => $level,
			'body' => $message,
		);
	}
	/**
	 * Write and clear all of the messages.
	 *
	 *     $log->write();
	 *
	 * @return  void
	 */
	public function write()
	{
		if ( empty($this->_messages) )
		{
			// There is nothing to write, move along
			return;
		}

		// Import all messages locally
		$messages = $this->_messages;

		// Reset the messages array
		$this->_messages = array();

		foreach( $this->_writers as $writer )
		{
			if ( empty($writer['levels']) )
			{
				// Write all of the messages
				$writer['object']->write($messages);
			}
			else
			{
				// Filtered messages
				$filtered = array();

				foreach( $messages as $message )
				{
					if ( in_array($message['level'], $writer['levels']) )
					{
						// Writer accepts this kind of message
						$filtered[] = $message;
					}
				}

				// Write the filtered messages
				$writer['object']->write($filtered);
			}
		}
	}
}