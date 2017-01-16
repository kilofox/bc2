<?php

class AutoloaderInit
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ($class === 'Kilofox\Bootphp\ClassLoader') {
            require __DIR__ . '/kilofox/bootphp/ClassLoader.php';
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
        $loader->set('App', __DIR__ . '/../application');

        $loader->register();

        return $loader;
    }

}

return AutoloaderInit::getLoader();
