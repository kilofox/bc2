<?php

// Set the default time zone.
date_default_timezone_set('UTC');

// Set the default language.
\Bootphp\I18n::lang('en-us');

/**
 * Initialize Core, setting the default options.
 *
 * The following options are available:
 *
 * - string		baseUrl		Path, and optionally domain, of your application	null
 * - string		cacheDir	Set the internal cache directory					APP_PATH/cache
 * - integer	cacheLife	Lifetime, in seconds, of items cached				60
 * - boolean	errors		Enable or disable error handling					true
 * - boolean	profile		Enable or disable internal profiling				true
 */
\Bootphp\Core::init([
    'baseUrl' => '/bootcms/',
    'cacheDir' => APP_PATH . '/cache',
    'cacheLife' => 60,
    'errors' => true,
    'profile' => true
]);

/**
 * Cookie Salt.
 *
 * If you have not defined a cookie salt in your Cookie class then uncomment the
 * line below and define a preferrably long salt.
 */
\Bootphp\Cookie::$salt = 'null';

// Set the routes.
\Bootphp\Route::set('admin', '(<directory>(/<controller>(/<id>)(/<action>)))', [
    'directory' => '(admin)',
    'id' => '\d+'
        ]
);
\Bootphp\Route::set('default', '(<controller>(/<id>)(/<action>))', ['id' => '\d+']);

