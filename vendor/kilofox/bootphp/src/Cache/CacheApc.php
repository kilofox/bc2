<?php

namespace Bootphp\Cache;
use Bootphp\Cache\Cache;
use Bootphp\Exception\ExceptionHandler;
/**
 * [BootPHP 缓存](api/Cache) APC 驱动。为 BootPHP 缓存库提供一个基于操作码的驱动。
 *
 * ### 配置举例
 *
 * 下面是 _apc_ 服务器配置的例子。
 *
 *     return array(
 *         'apc' => array(			// apc 分组
 *             'driver' => 'apc',	// 使用 APC 驱动
 *         ),
 *     );
 *
 * 在只需要一个缓存分组的情况下，如果分组命名为 `default`，那么在实例化缓存实例时，就不需要传递分组名。
 *
 * #### 一般缓存分组配置
 *
 * 名称   | 必需   | 描述
 * ------ | ------ | ------------------------
 * driver | __是__ | (_string_) 使用的驱动类型
 *
 * ### 系统需求
 *
 * *  APC PHP 扩展
 *
 * @package	BootPHP/缓存
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class CacheApc extends Cache
{
	/**
	 * 构造 APC 缓存驱动。这个方法不能被外部调用。该驱动必须用 Cache::instance() 方法来实例化。
	 *
	 * @param array $config 配置
	 * @throws ExceptionHandler
	 */
	protected function __construct(array $config)
	{
		if ( !extension_loaded('apc') )
		{
			throw new Cache_Exception('PHP APC 扩展不可用。');
		}
		parent::__construct($config);
	}
	/**
	 * 根据 id 获取缓存值条目。
	 *
	 *     // 从 apc 分组获取缓存条目
	 *     $data = Cache::instance('apc')->get('foo');
	 *
	 * @param	string	$id 缓存条目的id
	 * @return	mixed
	 * @throws ExceptionHandler
	 */
	public function get($id)
	{
		$data = apc_fetch($this->_sanitizeId($id), $success);
		return $success ? $data : NULL;
	}
	/**
	 * 用 id 和到期时间给缓存设值。
	 *
	 *     $data = 'bar';
	 *
	 *     // 将 'bar' 置给 apc 分组中的 'foo'，使用默认期限
	 *     Cache::instance('apc')->set('foo', $data);
	 *
	 *     // 将 'bar' 置给 apc 分组中的 'foo'，30秒过期
	 *     Cache::instance('apc')->set('foo', $data, 30);
	 *
	 * @param	string	$id 缓存条目的id
	 * @param	string	$data 置给缓存的数据
	 * @param	integer	$lifetime 以“秒”为单位的到期时间
	 * @return	boolean
	 */
	public function set($id, $data, $lifetime = NULL)
	{
		if ( $lifetime === NULL )
		{
			$lifetime = isset($this->_config['default_expire']) ? $this->_config['default_expire'] : Cache::DEFAULT_EXPIRE;
		}
		return apc_store($this->_sanitizeId($id), $data, $lifetime);
	}
	/**
	 * 根据 id 删除缓存条目。
	 *
	 *     // 从 apc 分组中删除 'foo' 条目
	 *     Cache::instance('apc')->delete('foo');
	 *
	 * @param	string	$id 要删除的条目id
	 * @return	boolean
	 */
	public function delete($id)
	{
		return apc_delete($this->_sanitizeId($id));
	}
	/**
	 * 删除全部缓存条目。
	 *
	 * 在使用共享内存的缓存系统时，请谨慎使用这个方法，它会清除系统内所有客户端的每个条目。
	 *
	 *     // 删除 apc 分组的全部缓存条目
	 *     Cache::instance('apc')->deleteAll();
	 *
	 * @return	boolean
	 */
	public function deleteAll()
	{
		return apc_clear_cache('user');
	}
}