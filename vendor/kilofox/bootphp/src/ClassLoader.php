<?php

namespace Kilofox\Bootphp;

/**
 * ClassLoader implements a PSR-4 class loader.
 *
 *     $loader = new \Kilofox\Bootphp\ClassLoader();
 *
 *     // Register classes with namespaces
 *     $loader->set('Bootphp', __DIR__ . '/kilofox/bootphp/src');
 *
 *     // Activate the autoloader
 *     $loader->register();
 */
class ClassLoader
{
    private $prefixDirsPsr4 = [];

    /**
     * Registers a PSR-4 directory for a given namespace.
     *
     * @param   string  $prefix The prefix/namespace
     * @param   string  $paths  The PSR-4 base directory
     */
    public function set($prefix, $path)
    {
        $this->prefixDirsPsr4[$prefix . '\\'] = $path;
    }

    /**
     * Registers this instance as an autoloader.
     */
    public function register()
    {
        spl_autoload_register([$this, 'loadClass'], true);
    }

    /**
     * Loads the given class or interface.
     *
     * @param   string  $class  The name of the class
     * @return  mixed   True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        $logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';

        foreach ($this->prefixDirsPsr4 as $prefix => $dir) {
            if (strpos($class, $prefix) === 0) {
                $file = $dir . DIRECTORY_SEPARATOR . substr($logicalPathPsr4, strlen($prefix));
                if (file_exists($file)) {
                    include $file;

                    return true;
                }
            }
        }

        return null;
    }

}
