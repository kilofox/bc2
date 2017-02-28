<?php

namespace Bootphp;

use Bootphp\BootphpException;

/**
 * Contains the most low-level helpers methods in Kohana:
 *
 * - Environment initialization
 * - Locating files within the cascading filesystem
 * - Auto-loading and transparent extension of classes
 * - Variable and path debugging
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Core
{
    /**
     * Release version
     */
    const VERSION = '2.0.0';
    // Common environment type constants for consistency and convenience
    const PRODUCTION = 10;
    const STAGING = 20;
    const TESTING = 30;
    const DEVELOPMENT = 40;
    // Security check that is added to all generated PHP files
    const FILE_SECURITY = '<?php defined(\'SYS_PATH\') or exit(\'Access Denied.\');';
    // Format of cache files: header, cache name, and data
    const FILE_CACHE = ":header \n\n// :name\n\n:data\n";

    /**
     * @var string  Current environment name
     */
    public static $environment = self::DEVELOPMENT;

    /**
     * @var boolean True if Kohana is running on windows
     */
    public static $is_windows = false;

    /**
     * @var boolean True if PHP safe mode is on
     */
    public static $safe_mode = false;

    /**
     * @var string
     */
    public static $content_type = 'text/html';

    /**
     * @var string  Character set of input and output
     */
    public static $charset = 'utf-8';

    /**
     * @var string  The name of the server Kohana is hosted upon
     */
    public static $server_name = '';

    /**
     * @var array   List of valid host names for this instance
     */
    public static $hostnames = array();

    /**
     * @var string  Base URL to the application
     */
    public static $baseUrl = '/';

    /**
     * @var string  Cache directory, used by [Core::cache]. Set by [Core::init]
     */
    public static $cacheDir;

    /**
     * @var integer Default lifetime for caching, in seconds, used by [Core::cache]. Set by [Core::init]
     */
    public static $cacheLife = 60;

    /**
     * @var boolean Whether to use internal caching for [Core::find_file], does not apply to [Core::cache]. Set by [Core::init]
     */
    public static $caching = false;

    /**
     * @var boolean Whether to enable [profiling](kohana/profiling). Set by [Core::init]
     */
    public static $profiling = true;

    /**
     * @var boolean Enable Kohana catching and displaying PHP errors and exceptions. Set by [Core::init]
     */
    public static $errors = true;

    /**
     * @var array   Types of errors to display at shutdown
     */
    public static $shutdown_errors = array(E_PARSE, E_ERROR, E_USER_ERROR);

    /**
     * @var boolean Set the X-Powered-By header
     */
    public static $expose = false;

    /**
     * @var Log     Logging object
     */
    public static $log;

    /**
     * @var  Config Config object
     */
    public static $config;

    /**
     * @var boolean Has [Core::init] been called?
     */
    protected static $_init = false;

    /**
     * @var array   Currently active modules
     */
    protected static $_modules = array();

    /**
     * @var array   Include paths that are used to find files
     */
    protected static $_paths = array(APP_PATH, SYS_PATH);

    /**
     * @var array   File path cache, used when caching is true in [Core::init]
     */
    protected static $_files = array();

    /**
     * @var boolean Has the file path cache changed during this execution?  Used internally when when caching is true in [Core::init]
     */
    protected static $_files_changed = false;

    /**
     * Initializes the environment:
     *
     * - Disables register_globals and magic_quotes_gpc
     * - Determines the current environment
     * - Set global settings
     * - Sanitizes GET, POST, and COOKIE variables
     * - Converts GET, POST, and COOKIE variables to the global character set
     *
     * The following settings can be set:
     *
     * Type      | Setting    | Description                                    | Default Value
     * ----------|------------|------------------------------------------------|---------------
     * `string`  | baseUrl   | The base URL for your application.  This should be the *relative* path from your DOCROOT to your `index.php` file, in other words, if Kohana is in a subfolder, set this to the subfolder name, otherwise leave it as the default.  **The leading slash is required**, trailing slash is optional.   | `"/"`
     * `string`  | cacheDir  | Kohana's cache directory.  Used by [Core::cache] for simple internal caching, like [Fragments](kohana/fragments) and **\[caching database queries](this should link somewhere)**.  This has nothing to do with the [Cache module](cache). | `APP_PATH."cache"`
     * `integer` | cacheLife | Lifetime, in seconds, of items cached by [Core::cache]         | `60`
     * `boolean` | errors     | Should Kohana catch PHP errors and uncaught Exceptions and show the `error_view`. See [Error Handling](kohana/errors) for more info. <br /> <br /> Recommended setting: `true` while developing, `false` on production servers. | `true`
     * `boolean` | profile    | Whether to enable the [Profiler](kohana/profiling). <br /> <br />Recommended setting: `true` while developing, `false` on production servers. | `true`
     *
     * @throws  BootphpException
     * @param   array   $settings   Array of settings.  See above.
     * @return  void
     * @uses    Core::globals
     * @uses    Core::sanitize
     * @uses    Core::cache
     * @uses    Profiler
     */
    public static function init(array $settings = null)
    {
        if (self::$_init) {
            // Do not allow execution twice
            return;
        }

        // Kohana is now initialized
        self::$_init = true;

        if (isset($settings['profile'])) {
            // Enable profiling
            self::$profiling = (bool) $settings['profile'];
        }

        // Start an output buffer
        ob_start();

        if (isset($settings['errors'])) {
            // Enable error handling
            self::$errors = (bool) $settings['errors'];
        }

        if (self::$errors === true) {
            // Enable Kohana exception handling, adds stack traces and error source.
            set_exception_handler(['Bootphp\BootphpException', 'handler']);

            // Enable Kohana error handling, converts all PHP errors to exceptions.
            set_error_handler(['Bootphp\Core', 'errorHandler']);
        }

        // Enable the Kohana shutdown handler, which catches E_FATAL errors.
        register_shutdown_function(['Bootphp\Core', 'shutdownHandler']);

        if (ini_get('register_globals')) {
            // Reverse the effects of register_globals
            self::globals();
        }

        if (isset($settings['expose'])) {
            self::$expose = (bool) $settings['expose'];
        }

        // Determine if we are running in a Windows environment
        self::$is_windows = (DIRECTORY_SEPARATOR === '\\');

        // Determine if we are running in safe mode
        self::$safe_mode = (bool) ini_get('safe_mode');

        if (isset($settings['cacheDir'])) {
            if (!is_dir($settings['cacheDir'])) {
                try {
                    // Create the cache directory
                    mkdir($settings['cacheDir'], 0755, true);

                    // Set permissions (must be manually set to fix umask issues)
                    chmod($settings['cacheDir'], 0755);
                } catch (Exception $e) {
                    throw new BootphpException('Could not create cache directory :dir', array(':dir' => Debug::path($settings['cacheDir'])));
                }
            }

            // Set the cache directory path
            self::$cacheDir = realpath($settings['cacheDir']);
        } else {
            // Use the default cache directory
            self::$cacheDir = APP_PATH . '/cache';
        }

        if (!is_writable(self::$cacheDir)) {
            throw new BootphpException('Directory :dir must be writable', array(':dir' => Debug::path(self::$cacheDir)));
        }

        if (isset($settings['cacheLife'])) {
            // Set the default cache lifetime
            self::$cacheLife = (int) $settings['cacheLife'];
        }

        if (isset($settings['caching'])) {
            // Enable or disable internal caching
            self::$caching = (bool) $settings['caching'];
        }

        if (self::$caching === true) {
            // Load the file path cache
            self::$_files = self::cache('self::find_file()');
        }

        if (isset($settings['charset'])) {
            // Set the system character set
            self::$charset = strtolower($settings['charset']);
        }

        if (function_exists('mb_internal_encoding')) {
            // Set the MB extension encoding to the same character set
            mb_internal_encoding(self::$charset);
        }

        if (isset($settings['baseUrl'])) {
            // Set the base URL
            self::$baseUrl = rtrim($settings['baseUrl'], '/') . '/';
        }

        // Sanitize all request variables
        $_GET = self::sanitize($_GET);
        $_POST = self::sanitize($_POST);
        $_COOKIE = self::sanitize($_COOKIE);

        // Load the logger if one doesn't already exist
        if (!self::$log instanceof Log) {
            self::$log = Log::instance();
        }

        // Load the config
        self::$config = new Config();
    }

    /**
     * Cleans up the environment:
     *
     * - Restore the previous error and exception handlers
     * - Destroy the self::$log and self::$config objects
     *
     * @return  void
     */
    public static function deinit()
    {
        if (self::$_init) {
            // Removed the autoloader
            spl_autoload_unregister(array('Kohana', 'auto_load'));

            if (self::$errors) {
                // Go back to the previous error handler
                restore_error_handler();

                // Go back to the previous exception handler
                restore_exception_handler();
            }

            // Destroy objects created by init
            self::$log = self::$config = null;

            // Reset internal storage
            self::$_modules = self::$_files = array();
            self::$_paths = array(APP_PATH, SYS_PATH);

            // Reset file cache status
            self::$_files_changed = false;

            // Kohana is no longer initialized
            self::$_init = false;
        }
    }

    /**
     * Reverts the effects of the `register_globals` PHP setting by unsetting
     * all global variables except for the default super globals (GPCS, etc),
     * which is a [potential security hole.][ref-wikibooks]
     *
     * This is called automatically by [Core::init] if `register_globals` is
     * on.
     *
     *
     * [ref-wikibooks]: http://en.wikibooks.org/wiki/PHP_Programming/Register_Globals
     *
     * @return  void
     */
    public static function globals()
    {
        if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS'])) {
            // Prevent malicious GLOBALS overload attack
            echo "Global variable overload attack detected! Request aborted.\n";

            // Exit with an error status
            exit(1);
        }

        // Get the variable names of all globals
        $global_variables = array_keys($GLOBALS);

        // Remove the standard global variables from the list
        $global_variables = array_diff($global_variables, array(
            '_COOKIE',
            '_ENV',
            '_GET',
            '_FILES',
            '_POST',
            '_REQUEST',
            '_SERVER',
            '_SESSION',
            'GLOBALS',
        ));

        foreach ($global_variables as $name) {
            // Unset the global variable, effectively disabling register_globals
            unset($GLOBALS[$name]);
        }
    }

    /**
     * Recursively sanitizes an input variable:
     *
     * - Normalizes all newlines to LF
     *
     * @param   mixed   $value  Any variable
     * @return  mixed   Sanitized variable
     */
    public static function sanitize($value)
    {
        if (is_array($value) || is_object($value)) {
            foreach ($value as $key => $val) {
                // Recursively clean each value
                $value[$key] = self::sanitize($val);
            }
        } elseif (is_string($value)) {
            if (strpos($value, "\r") !== false) {
                // Standardize newlines
                $value = str_replace(array("\r\n", "\r"), "\n", $value);
            }
        }

        return $value;
    }

    /**
     * Loads a file within a totally empty scope and returns the output:
     *
     *     $foo = Core::load('foo.php');
     *
     * @param   string  $file
     * @return  mixed
     */
    public static function load($file)
    {
        return include $file;
    }

    /**
     * Get a message from a file. Messages are arbitrary strings that are stored
     * in the `messages/` directory and reference by a key. Translation is not
     * performed on the returned values.  See [message files](kohana/files/messages)
     * for more information.
     *
     *     // Get "username" from messages/text.php
     *     $username = Core::message('text', 'username');
     *
     * @param   string  $file       file name
     * @param   string  $path       key path to get
     * @param   mixed   $default    default value if the path does not exist
     * @return  string  message string for the given path
     * @return  array   complete message list, when no path is specified
     * @uses    Arr::merge
     * @uses    Arr::path
     */
    public static function message($file, $path = null, $default = null)
    {
        static $messages;

        if (!isset($messages[$file])) {
            // Create a new message list
            $messages[$file] = array();

            if ($files = self::find_file('messages', $file)) {
                foreach ($files as $f) {
                    // Combine all the messages recursively
                    $messages[$file] = Arr::merge($messages[$file], self::load($f));
                }
            }
        }

        if ($path === null) {
            // Return all of the messages
            return $messages[$file];
        } else {
            // Get a message using the path
            return Arr::path($messages[$file], $path, $default);
        }
    }

    /**
     * PHP error handler, converts all errors into ErrorExceptions. This handler
     * respects error_reporting settings.
     *
     * @throws  ErrorException
     * @return  true
     */
    public static function errorHandler($code, $error, $file = null, $line = null)
    {
        if (error_reporting() & $code) {
            // This error is not suppressed by current error reporting settings
            // Convert the error into an ErrorException
            throw new \ErrorException($error, $code, 0, $file, $line);
        }

        // Do not execute the PHP error handler
        return true;
    }

    /**
     * Catches errors that are not caught by the error handler, such as E_PARSE.
     *
     * @uses    BootphpException::handler
     * @return  void
     */
    public static function shutdownHandler()
    {
        if (!self::$_init) {
            // Do not execute when not active
            return;
        }

        try {
            if (self::$caching === true AND self::$_files_changed === true) {
                // Write the file path cache
                self::cache('Core::find_file()', self::$_files);
            }
        } catch (Exception $e) {
            // Pass the exception to the handler
            BootphpException::handler($e);
        }

        if (self::$errors AND $error = error_get_last() AND in_array($error['type'], self::$shutdown_errors)) {
            // Clean the output buffer
            ob_get_level() AND ob_clean();

            // Fake an exception for nice debugging
            BootphpException::handler(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));

            // Shutdown now to avoid a "death loop"
            exit(1);
        }
    }

}
