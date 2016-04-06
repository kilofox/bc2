<?php
// Set the MB extension encoding to the character set
mb_internal_encoding('UTF-8');

// Define the absolute path for the application directory
define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'applications');

// Define the start time of the application, used for profiling.
defined('START_TIME') || define('START_TIME', microtime(true));

// Define the memory usage at the start of the application, used for profiling.
defined('START_MEMORY') || define('START_MEMORY', memory_get_usage());

// Autoloader
require __DIR__ . '/vendor/autoload.php';

// Bootstrap the application
require APP_PATH . '/bootstrap.php';

// Bootphp application
$app = new \Bootphp\App(require __DIR__ . '/configs/global.php');

// Execute the main request
$app->run();
