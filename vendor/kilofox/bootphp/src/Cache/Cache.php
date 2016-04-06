<?php

namespace Bootphp\Cache;

/**
 * Bootphp Cache provides a common interface to a variety of caching engines. Tags are
 * supported where available natively to the cache system. Bootphp Cache supports multiple
 * instances of cache engines through a grouped singleton pattern.
 *
 * ### Supported cache engines
 *
 * *  [APC](http://php.net/manual/zh/book.apc.php)
 * *  文件
 * *  [Memcache](http://memcached.org/)
 *
 * ### 缓存介绍
 *
 * 缓存的实现应该考虑一下。通常，缓存资源的结果比重新处理它们更快。选择哪个引擎，如何缓存，什么时候缓存，是至关重要的。
 * PHP APC 是目前最快的可用缓存系统之一，紧接着的是 Memcache。
 * 文件缓存是最慢的缓存方式，但是比重新处理一组复杂的指令要快。
 *
 * 使用内存的缓存引擎比基于文件的方案要快得多。但内存是有限的，而磁盘空间是充足的。如果缓存大型数据集，最好使用文件缓存。
 *
 * ### 配置设置
 *
 * BootPHP 缓存使用配置分组来创建缓存实例。配置分组可以使用任何支持的驱动。如有需要，连续的分组使用相同的驱动类型。
 *
 * #### 配置举例
 *
 * 下面是 _memcache_ 服务器配置的一个例子。
 *
 *     return array(
 *         'default' => array(			// 默认分组
 *             'driver' => 'memcache',	// 使用 Memcache 驱动
 *             'servers' => array(		// 可用的服务器定义
 *                 array(
 *                     'host' => 'localhost',
 *                     'port' => 11211,
 *                     'persistent' => false
 *                 )
 *             ),
 *             'compression' => false,	// 使用压缩吗？
 *         ),
 *     );
 *
 * 在只需要一个缓存分组的情况下，如果分组命名为 `default`，那么在实例化缓存实例时，就不需要传递分组名。
 *
 * #### 一般缓存分组配置
 *
 * 下面的设置适用于所有类型的缓存驱动。
 *
 * 名称   | 必需   | 描述
 * ------ | ------ | -------------------------
 * driver | __是__ | (_string_) 使用的驱动类型
 *
 * 每一个特定驱动的文档中，都有配置详情。
 *
 * @package	BootPHP/缓存
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
abstract class Cache {

	const DEFAULT_EXPIRE = 3600;

	/**
	 * @var string 使用的默认驱动
	 */
	public static $default = 'file';

	/**
	 * @var	缓存实例
	 */
	public static $instances = array();

	/**
	 * 创建缓存分组的单例。如果未提供分组，则使用 __default__ 分组。
	 *
	 *     // 创建一个默认分组的实例
	 *     $default_group = Cache::instance();
	 *
	 *     // 创建一个分组实例
	 *     $foo_group = Cache::instance('foo');
	 *
	 *     // 直接访问实例化的分组
	 *     $foo_group = Cache::$instances['foo'];
	 *
	 * @param	string	$group 要使用的缓存分组名 [可选]
	 * @return	Cache
	 * @throws ExceptionHandler
	 */
	public static function instance($group = NULL)
	{
		// 如果未提供分组，则使用默认设置
		if ( $group === NULL )
		{
			$group = self::$default;
		}
		// 如果当前分组已经初始化，则返回它
		if ( isset(self::$instances[$group]) )
		{
			return self::$instances[$group];
		}
		$configs = require APP_PATH . '/../configs/cache.php';
		if ( !isset($configs[$group]) )
		{
			throw new ExceptionHandler('加载缓存分组 ' . $group . ' 失败');
		}
		$config = $configs[$group];
		// 创建一个新的缓存类型的实例
		$cache_class = 'Bootphp\\Cache\\Cache' . ucfirst($config['driver']);
		self::$instances[$group] = new $cache_class($config);
		// 返回实例
		return self::$instances[$group];
	}

	/**
	 * @var 配置
	 */
	protected $_config = array();

	/**
	 * 确保遵守单例模式，加载默认期限。
	 *
	 * @param array 配置
	 */
	protected function __construct(array $config)
	{
		$this->config($config);
	}

	/**
	 * 获得或设置配置。如果没有提供参数，则返回当前配置。否则，为这个类设置配置。
	 *
	 *     // 覆盖所有配置
	 *     $cache->config(array('driver' => 'memcache', '...'));
	 *
	 *     // 设置新的配置
	 *     $cache->config('servers', array(
	 *         'foo' => 'bar',
	 *         '...'
	 *     ));
	 *
	 *     // 获得一个配置
	 *     $servers = $cache->config('servers);
	 *
	 * @param mixed 为数组设置的键，要么是数组，要么是配置路径
	 * @param mixed 与键关联的值
	 * @return	mixed
	 */
	public function config($key = NULL, $value = NULL)
	{
		if ( $key === NULL )
			return $this->_config;
		if ( is_array($key) )
		{
			$this->_config = $key;
		}
		else
		{
			if ( $value === NULL )
				return $this->_config[$key];
			$this->_config[$key] = $value;
		}
		return $this;
	}

	/**
	 * 重载 __clone() 方法，防止复制
	 *
	 * @return	void
	 * @throws ExceptionHandler
	 */
	final public function __clone()
	{
		throw new Exception\ExceptionHandler('禁止复制缓存对象');
	}

	/**
	 * 根据 id 获取缓存值条目。
	 *
	 *     // 从默认分组获取缓存条目
	 *     $data = Cache::instance()->get('foo');
	 *
	 *     // 从 memcache 分组获取缓存条目
	 *     $data = Cache::instance('memcache')->get('foo');
	 *
	 * @param	string	$id 缓存条目的id
	 * @return	mixed
	 * @throws ExceptionHandler
	 */
	abstract public function get($id);

	/**
	 * 用 id 和生命周期给缓存设值。
	 *
	 *     $data = 'bar';
	 *
	 *     // 将 'bar' 置给默认分组中 'foo'，使用默认期限
	 *     Cache::instance()->set('foo', $data);
	 *
	 *     // 将 'bar' 置给默认分组中的 'foo'，30秒过期
	 *     Cache::instance()->set('foo', $data, 30);
	 *
	 *     // 将 'bar' 置给 memcache 分组中的 'foo'，10分钟过期
	 *     Cache::instance('memcache')->set('foo', $data, 600);
	 *
	 * @param	string	缓存条目的id
	 * @param	string	置给缓存的数据
	 * @param	integer	以“秒”为单位的生命周期
	 * @return	boolean
	 */
	abstract public function set($id, $data, $lifetime = 3600);

	/**
	 * 根据 id 删除缓存条目
	 *
	 *     // 从默认分组中删除 'foo' 条目
	 *     Cache::instance()->delete('foo');
	 *
	 *     // 从 memcache 分组中删除 'foo' 条目
	 *     Cache::instance('memcache')->delete('foo');
	 *
	 * @param	string	要删除的条目id
	 * @return	boolean
	 */
	abstract public function delete($id);

	/**
	 * 删除全部缓存条目。
	 *
	 * 在使用共享内存的缓存系统时，请谨慎使用这个方法，它会清除系统内所有客户端的每个条目。
	 *
	 *     // 删除默认分组的全部缓存条目
	 *     Cache::instance()->deleteAll();
	 *
	 *     // 删除 memcache 分组的全部缓存条目
	 *     Cache::instance('memcache')->deleteAll();
	 *
	 * @return	boolean
	 */
	abstract public function deleteAll();

	/**
	 * 用下划线替换麻烦的字符。
	 *
	 *     // 清理缓存id
	 *     $id = $this->_sanitizeId($id);
	 *
	 * @param	string	要清理的缓存id
	 * @return	string
	 */
	protected function _sanitizeId($id)
	{
		// Change slashes and spaces to underscores
		return str_replace(array('/', '\\', ' '), '_', $id);
	}
}