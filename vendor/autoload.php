<?php

class AutoloaderInit
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ($class === 'Kilofox\Bootphp\ClassLoader') {
            require __DIR__ . '/kilofox/bootphp/src/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (self::$loader !== null) {
            return self::$loader;
        }

        spl_autoload_register(['AutoloaderInit', 'loadClassLoader'], true, true);
        self::$loader = $loader = new \Kilofox\Bootphp\ClassLoader();
        spl_autoload_unregister(['AutoloaderInit', 'loadClassLoader']);

        $loader->set('Bootphp', __DIR__ . '/kilofox/bootphp/src');
        $loader->set('Psr\\Http\\Message', __DIR__ . '/psr/http-message/src');
        $loader->set('Psr\\Log', __DIR__ . '/psr/log/Psr/Log');
        $loader->set('Psr\\Cache', __DIR__ . '/psr/cache/src');
        $loader->set('App', __DIR__ . '/../application');
        $loader->set('Michelf', __DIR__ . '/Michelf');

        $loader->register();

        return $loader;
    }

}

return AutoloaderInit::getLoader();
