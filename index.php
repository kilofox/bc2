<?php

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @link http://www.php.net/manual/errorfunc.configuration#ini.error-reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 */
// Define the absolute paths for configured directories
define('ROOT_PATH', __DIR__);
define('SYS_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'kilofox' . DIRECTORY_SEPARATOR . 'bootphp' . DIRECTORY_SEPARATOR . 'src');
define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'application');

/**
 * Define the start time of the application, used for profiling.
 */
if (!defined('START_TIME')) {
    define('START_TIME', microtime(true));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if (!defined('START_MEMORY')) {
    define('START_MEMORY', memory_get_usage());
}

// Enable the Bootphp auto-loader.
require __DIR__ . '/vendor/autoload.php';

// Bootstrap the application
require APP_PATH . '/bootstrap.php';

if (PHP_SAPI == 'cli') { // Try and load minion
    class_exists('Minion_Task') OR die('Please enable the Minion module for CLI support.');
    set_exception_handler(array('Minion_Exception', 'handler'));

    Minion_Task::factory(Minion_CLI::options())->execute();
} else {
    /**
     * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
     * If no source is specified, the URI will be automatically detected.
     */
    echo \Bootphp\Request::factory(true, [], false)
        ->execute()
        ->send_headers(true)
        ->body();
}
