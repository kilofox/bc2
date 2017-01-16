<?php

namespace Bootphp;

/**
 * Kohana exception class. Translates exceptions using the [I18n] class.
 *
 * @package    Bootphp
 * @category   Exceptions
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class BootphpException extends \Exception
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
        E_DEPRECATED => 'Deprecated',
    );

    /**
     * Creates a new translated exception.
     *
     *     throw new BootphpException('Something went terrible wrong, :user',
     *         array(':user' => $user));
     *
     * @param   string          $message    error message
     * @param   array           $variables  translation variables
     * @param   integer|string  $code       the exception code
     * @param   Exception       $previous   Previous exception
     * @return  void
     */
    public function __construct($message = '', $code = 0)
    {
        // Pass the message and integer code to the parent
        parent::__construct($message, (int) $code);
    }

    /**
     * Magic object-to-string method.
     *
     *     echo $exception;
     *
     * @uses    BootphpException::text
     * @return  string
     */
    public function __toString()
    {
        return self::text($this);
    }

    /**
     * Inline exception handler, displays the error message, source of the
     * exception, and the stack trace of the error.
     *
     * @uses    self::response
     * @param   Exception  $e
     * @return  void
     */
    public static function handler(\Exception $e)
    {
        try {
            // Log the exception
            self::log($e);

            // Generate the response
            $response = self::response($e);
        } catch (\Exception $e) {
            /**
             * Things are going *really* badly for us, We now have no choice
             * but to bail. Hard.
             */
            // Clean the output buffer if one exists
            ob_get_level() AND ob_clean();

            // Set the Status code to 500, and Content-Type to text/plain.
            header('Content-Type: text/plain; charset=UTF-8', true, 500);

            echo self::text($e);

            exit(1);
        }

        // Send the response to the browser
        echo $response->send_headers()->body();

        exit(1);
    }

    /**
     * Logs an exception.
     *
     * @uses    BootphpException::text
     * @param   Exception  $e
     * @param   int        $level
     * @return  void
     */
    public static function log(\Exception $e, $level = Log::EMERGENCY)
    {
        // Create a text version of the exception
        $error = self::text($e);

        $log = Log::instance();

        // Add this exception to the log
        $log->add($level, $error, null, array('exception' => $e));

        // Make sure the logs are written
        $log->write();
    }

    /**
     * Get a single line of text representing the exception:
     *
     * Error [ Code ]: Message ~ File [ Line ]
     *
     * @param   Exception  $e
     * @return  string
     */
    public static function text(\Exception $e)
    {
        return sprintf('%s [ %s ]: %s ~ %s [ %d ]', get_class($e), $e->getCode(), strip_tags($e->getMessage()), Debug::path($e->getFile()), $e->getLine());
    }

    /**
     * Get a Response object representing the exception
     *
     * @uses    BootphpException::text
     * @param   Exception  $e
     * @return  Response
     */
    public static function response(\Exception $e)
    {
        try {
            // Get the exception information
            $class = get_class($e);
            $code = $e->getCode();
            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();

            /**
             * HTTP_Exceptions are constructed in the HTTP_Exception::factory()
             * method. We need to remove that entry from the trace and overwrite
             * the variables from above.
             */
            if ($e instanceof HTTP_Exception AND $trace[0]['function'] == 'factory') {
               // extract(array_shift($trace));
            }


            if ($e instanceof \ErrorException) {
                if (isset(self::$phpErrors[$code])) {
                    // Use the human-readable error name
                    $code = self::$phpErrors[$code];
                }
            }

            // Instantiate the error view.
            $view = View::factory(SYS_PATH . '/BootphpException/Views/error.php', get_defined_vars());

            // Prepare the response object.
            $response = Response::factory();

            // Set the response status
            $response->status(key_exists($code, Response::$messages) ? $code : 500);

            // Set the response headers
            $response->headers('Content-Type', 'text/html; charset=UTF-8');

            // Set the response body
            $response->body($view->render());
        } catch (\Exception $e) {
            /**
             * Things are going badly for us, Lets try to keep things under control by
             * generating a simpler response object.
             */
            $response = Response::factory();
            $response->status(500);
            $response->headers('Content-Type', 'text/plain');
            $response->body(self::text($e));
        }

        return $response;
    }

}
