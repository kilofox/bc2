<?php

namespace Bootphp\Exception;
use Bootphp\BootPHP;
use Bootphp\Log;
use Bootphp\Request;
use Bootphp\Response;
/**
 * BootPHP exception class.
 *
 * @package	BootPHP
 * @category	Exceptions
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class ExceptionHandler extends \Exception
{
	/**
	 * @var  array  PHP error code => human readable name
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
	 * Creates a new translated exception.
	 *
	 * 	 throw new ExceptionHandler('Something went terrible wrong');
	 *
	 * @param   string          $message    error message
	 * @param   integer|string  $code       the exception code
	 * @return	void
	 */
	public function __construct($message, $code = 0)
	{
		// Pass the message and integer code to the parent
		parent::__construct($message, (int)$code);

		// Save the unmodified code
		$this->code = $code;
	}
	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * @uses	ExceptionHandler::text
	 * @param	Exception	$e
	 * @return	boolean
	 */
	public static function handler(\Exception $e)
	{
		try
		{
			// Log the exception
			self::log($e);

			// Generate the response
			$response = self::response($e);

			// Send the response to the browser
			$response->send();

			exit(1);
		}
		catch( Exception $e )
		{
			// Things are going *really* badly for us, We now have no choice
			// but to bail. Hard.
			// Clean the output buffer if one exists
			ob_get_level() && ob_clean();
			// Set the Status code to 500, and Content-Type to text/plain.
			header('Content-Type: text/plain; charset=UTF-8', true, 500);

			echo self::text($e);

			exit(1);
		}
	}
	/**
	 * Logs an exception.
	 *
	 * @uses ExceptionHandler::text
	 * @param Exception $e
	 * @param int $level
	 * @return	void
	 */
	public static function log(\Exception $e, $level = Log::EMERGENCY)
	{
		$log = Log::instance(APP_PATH . '/logs');

		// Create a text version of the exception
		$error = self::text($e);

		// Add this exception to the log
		$log->add($level, $error);

		// Make sure the logs are written
		$log->write();
	}
	/**
	 * Get a single line of text representing the exception:
	 *
	 * Error [ Code ]: Message ~ File [ Line ]
	 *
	 * @param Exception  $e
	 * @return	string
	 */
	public static function text(\Exception $e)
	{
		return get_class($e) . ' [ ' . $e->getCode() . ' ]: ' . strip_tags($e->getMessage()) . ' ~ ' . \Bootphp\Debug::path($e->getFile()) . ' [ ' . $e->getLine() . ' ]';
	}
	/**
	 * Get a Response object representing the exception.
	 *
	 * @uses ExceptionHandler::text
	 * @param Exception $e
	 * @return	Response
	 */
	public static function response(\Exception $e)
	{
		try
		{
			// Get the exception information
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
					// Use the human-readable error name
					$code = self::$phpErrors[$code];
				}
			}

			// Instantiate the error view
			$view = new \Bootphp\View();
			$view->path(__DIR__ . '/Views/');
			$view->file('error');
			$view->layout(false);
			$view->set(get_defined_vars());

			// Prepare the response object
			$response = new Response();

			// Set the response status
			$response->status($response->statusText($code) ? $code : 500);

			// Set the response body
			$response->content($view->render());
		}
		catch( Exception $e )
		{
			// Things are going badly for us, Lets try to keep things under control by
			// generating a simpler response object.
			$response = new Response();
			$response->status(500);
			$response->header('Content-Type', 'text/plain');
			$response->content(self::text($e));
		}
		return $response;
	}
}