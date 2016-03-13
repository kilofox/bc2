<?php
class AutoloaderInit
{
	private static $loader;
	public static function loadClassLoader($class)
	{
		if ( 'Kilofox\Bootphp\AutoloaderClass' === $class )
		{
			require __DIR__ . '/kilofox/bootphp/AutoloaderClass.php';
		}
	}
	public static function getLoader()
	{
		if ( NULL !== self::$loader )
		{
			return self::$loader;
		}
		spl_autoload_register(array('AutoloaderInit', 'loadClassLoader'), true, true);
		// 初始化加载器
		self::$loader = new \Kilofox\Bootphp\AutoloaderClass;
		// 注册自动加载器
		self::$loader->register();
		// 为命名空间前缀注册基目录
		self::$loader->addNamespace('Bootphp', __DIR__ . '/kilofox/bootphp/src');
		self::$loader->addNamespace('App', __DIR__ . '/../application');
		self::$loader->addNamespace('Michelf', __DIR__ . '/michelf');
		spl_autoload_unregister(array('AutoloaderInit', 'loadClassLoader'));
		return self::$loader;
	}
}