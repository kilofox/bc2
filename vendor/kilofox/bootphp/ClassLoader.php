<?php

namespace Kilofox\Bootphp;
/**
 * ClassLoader implements a PSR-4 class loader.
 *
 *     $loader = new \Kilofox\Bootphp\ClassLoader();
 *
 *     // Register classes with namespaces
 *     $loader->set('Bootphp', __DIR__.'/kilofox/bootphp/src');
 *
 *     // Activate the autoloader
 *     $loader->register();
 */
class ClassLoader
{
	private $prefixDirsPsr4 = array();
	/**
	 * Registers a set of PSR-4 directories for a given namespace,
	 * replacing any others previously set for this namespace.
	 *
	 * @param string		$prefix The prefix/namespace, with trailing '\\'
	 * @param array|string	$paths The PSR-4 base directories
	 *
	 * @throws \InvalidArgumentException
	 */
	public function set($prefix, $paths)
	{
		$this->prefixDirsPsr4[$prefix . '\\'] = $paths;
	}
	/**
	 * Registers this instance as an autoloader.
	 *
	 * @param bool $prepend Whether to prepend the autoloader or not
	 */
	public function register()
	{
		spl_autoload_register([$this, 'loadClass'], true);
	}
	/**
	 * Loads the given class or interface.
	 *
	 * @param	string		$class The name of the class
	 * @return	bool|null	True if loaded, null otherwise
	 */
	public function loadClass($class)
	{
		$logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';

		foreach( $this->prefixDirsPsr4 as $prefix => $dir )
		{
			if ( strpos($class, $prefix) === 0 )
			{
				$file = $dir . DIRECTORY_SEPARATOR . substr($logicalPathPsr4, strlen($prefix));
				if ( file_exists($file) )
				{
					include $file;

					return true;
				}
			}
		}

		return NULL;
	}
}