<?php

namespace Kilofox\Bootphp;
/**
 * 一个通用的实现的例子，包括单个命名空间前缀允许多个基目录的可选功能。
 *
 * 假设在文件系统中有一个 foo-bar 类包，路径如下：
 *
 *     /path/to/packages/foo-bar/
 *         src/
 *             Baz.php             # Foo\Bar\Baz
 *             Qux/
 *                 Quux.php        # Foo\Bar\Qux\Quux
 *         tests/
 *             BazTest.php         # Foo\Bar\BazTest
 *             Qux/
 *                 QuuxTest.php    # Foo\Bar\Qux\QuuxTest
 *
 * 将命名空间前缀 \Foo\Bar\ 添加到类文件的路径中：
 *
 *      <?php
 *      // 初始化加载器
 *      $loader = new \Kilofox\Bootphp\AutoloaderClass;
 *
 *      // 注册自动加载器
 *      $loader->register();
 *
 *      // 为命名空间前缀注册基目录
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/src');
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');
 *
 * 下面一行会使自动加载器从 /path/to/packages/foo-bar/src/Qux/Quux.php 尝试加载 \Foo\Bar\Qux\Quux 类：
 *
 *      <?php
 *      new \Foo\Bar\Qux\Quux;
 *
 * 下面一行会使自动加载器从 /path/to/packages/foo-bar/tests/Qux/QuuxTest.php 尝试加载 \Foo\Bar\Qux\QuuxTest 类：
 *
 *      <?php
 *      new \Foo\Bar\Qux\QuuxTest;
 */
class AutoloaderClass
{
	/**
	 * 一个关联数组，键为命名空间前缀，值为那个命名空间中的类的基目录的数组。
	 *
	 * @var array
	 */
	protected $prefixes = [];
	/**
	 * 用 SPL 自动加载器堆栈注册加载器。
	 *
	 * @return void
	 */
	public function register()
	{
		spl_autoload_register(array($this, 'loadClass'));
	}
	/**
	 * 为命名空间前缀添加基目录。
	 *
	 * @param string $prefix 命名空间前缀
	 * @param string $baseDir 命名空间中的类文件的基目录
	 * @param bool $prepend 如果为真，在堆栈前面加上基目录，而不是追加；这使得它被首先搜索，而不是最后搜索。
	 * @return void
	 */
	public function addNamespace($prefix, $baseDir, $prepend = false)
	{
		// 规范命名空间前缀
		$prefix = trim($prefix, '\\') . '\\';
		// 规范基目录，后面带一个分隔符
		$baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';
		// 初始化命名空间前缀数组
		if ( isset($this->prefixes[$prefix]) === false )
		{
			$this->prefixes[$prefix] = [];
		}
		// 为命名空间前缀保留基目录
		if ( $prepend )
		{
			array_unshift($this->prefixes[$prefix], $baseDir);
		}
		else
		{
			array_push($this->prefixes[$prefix], $baseDir);
		}
	}
	/**
	 * 用给定的类名加载类文件。
	 *
	 * @param string $class 完全限定类名
	 * @return mixed 成功时为映射文件名，失败时为布尔值 false 。
	 */
	public function loadClass($class)
	{
		// 当前命名空间前缀
		$prefix = $class;
		// 通过完全限定类名的命名空间的名字查找映射的文件名
		while( false !== $pos = strrpos($prefix, '\\') )
		{
			// 保留前缀末尾的命名空间分隔符
			$prefix = substr($class, 0, $pos + 1);
			// 其余的是相对类名
			$relativeClass = substr($class, $pos + 1);
			// 尝试加载前缀和相对类名的映射文件
			$mappedFile = $this->loadMappedFile($prefix, $relativeClass);
			if ( $mappedFile )
			{
				return $mappedFile;
			}
			// 移除末尾的命名空间分隔符，迭代下一个 strrpos()
			$prefix = rtrim($prefix, '\\');
		}
		// 根本没找到映射文件
		return false;
	}
	/**
	 * 加载命名空间前缀和相对类的映射文件。
	 *
	 * @param string $prefix 命名空间前缀
	 * @param string $relativeClass 相对类名
	 * @return mixed 如果存在可以加载的映射文件，则为加载的映射文件名，否则为布尔值 false 。
	 */
	protected function loadMappedFile($prefix, $relativeClass)
	{
		// 这个命名空间前缀有基目录吗？
		if ( !isset($this->prefixes[$prefix]) )
		{
			return false;
		}
		// 翻找这个命名空间前缀的基目录
		foreach( $this->prefixes[$prefix] as $baseDir )
		{
			// 用基目录替换命名空间前缀
			// 相对类名中，用目录分隔符替换命名空间分隔符
			// 以 .php 结尾
			$file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
			// 如果映射文件存在，包含它
			if ( $this->requireFile($file) )
			{
				// 是的，完成了
				return $file;
			}
		}
		// 根本没找到
		return false;
	}
	/**
	 * 如果文件存在，从文件系统包含进来。
	 *
	 * @param string $file 要包含的文件
	 * @return bool 如果文件存在，为真，否则为假。
	 */
	protected function requireFile($file)
	{
		if ( file_exists($file) )
		{
			require $file;
			return true;
		}
		return false;
	}
}