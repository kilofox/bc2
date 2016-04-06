<?php

namespace Bootphp\Cache;
use Bootphp\Cache\Cache;
use Bootphp\Exception\ExceptionHandler;
/**
 * [BootPHP 缓存](api/Cache) Memcache 驱动。
 *
 * ### 配置举例
 *
 * 下面是 _memcache_ 服务器配置的例子。
 *
 *     return array(
 *         'memcache' => array(			// memcache 分组
 *             'driver' => 'memcache',	// 使用 Memcache 驱动
 *             'servers' => array(		// 可用的服务器定义
 *                 // 第一台 memcache 服务器
 *                 array(
 *                     'host' => 'localhost',
 *                     'port' => 11211,
 *                     'persistent' => false
 *                     'weight' => 1,
 *                     'timeout' => 1,
 *                     'retry_interval' => 15,
 *                     'status' => true,
 * 			           'instant_death' => true,
 *                     'failure_callback' => array($this, '_failedRequest')
 *                 ),
 *                 // 第二台 memcache 服务器
 *                 array(
 *                     'host' => '192.168.1.5',
 *                     'port' => 22122,
 *                     'persistent' => true
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
 * 名称	       | 必需   | 描述
 * ----------- | ------ | ---------------------------------------------------------------
 * driver	   | __是__ | (_string_) 使用的驱动类型
 * servers	   | __是__ | (_array_) 服务器详情的关联数组，必须包含 __host__ 键。（参阅下面的 _Memcache 服务器配置_）
 * compression | __否__ | (_boolean_) 缓存时使用数据压缩
 *
 * #### Memcache 服务器配置
 *
 * 下面的设置应该在定义每台 memcache 服务器时使用
 *
 * 名称             | 必需   | 描述
 * ---------------- | ------ | ---------------------------------------------------------------
 * host             | __是__ | (_string_) Memcached 服务器监听主机，例如 __localhost__、__127.0.0.1__ 或 __memcache.domain.tld__
 * port             | __否__ | (_integer_) Memcached 服务器监听端口。默认为 __11211__
 * persistent       | __否__ | (_boolean_) 控制是否使用持久连接。默认为 __true__
 * weight           | __否__ | (_integer_) 为该服务器创建的桶数，用来控制它被选中的概率。这个概率是相对于全部服务器的总权重的。默认为 __1__
 * timeout          | __否__ | (_integer_) 连接到守护进程的秒数。修改此值之前请三思。如果连接太慢，将失去所有的缓存优势。默认为 __1__
 * retry_interval   | __否__ | (_integer_) 服务器发生故障后多久重试，默认值为 15 秒。该参数设置为 -1 则不做重试。默认为 __15__
 * status           | __否__ | (_boolean_) 控制服务器是否标记为在线。默认为 __true__
 * failure_callback | __否__ | (_[callback](http://php.net/manual/zh/language.pseudo-types.php#language.types.callback)_) 允许用户指定一个在发生错误时运行的回调函数。这个回调在故障转移之前运行。这个函数有两个参数，即失效服务器的主机名和端口。默认为 __NULL__
 *
 * ### 系统需求
 *
 * *  Memcache PHP 扩展
 * *  Zlib
 *
 * @package	BootPHP/缓存
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class CacheMemcache extends Cache
{
	// Memcache 有一个30天的最大缓存到期时间
	const CACHE_CEILING = 2592000;
	/**
	 * Memcache 源
	 *
	 * @var Memcache
	 */
	protected $_memcache;
	/**
	 * 存储值时使用的标志
	 *
	 * @var string
	 */
	protected $_flags;
	/**
	 * memcached 服务器的默认配置
	 *
	 * @var array
	 */
	protected $_defaultConfig = array();
	/**
	 * 构造 Memcache 缓存驱动。这个方法不能被外部调用。该驱动必须用 Cache::instance() 方法来实例化。
	 *
	 * @param array $config 配置
	 * @throws ExceptionHandler
	 */
	protected function __construct(array $config)
	{
		// 检查 memcache 扩展
		if ( !extension_loaded('memcache') )
		{
			throw new ExceptionHandler('PHP Memcache 扩展不可用。');
		}
		parent::__construct($config);
		// 设置 Memcache
		$this->_memcache = new Memcache;
		// 从配置中加载服务器
		$servers = isset($this->_config['servers']) ? $this->_config['servers'] : NULL;
		if ( !$servers )
		{
			// 没找到服务器，抛出异常
			throw new ExceptionHandler('配置未定义 Memcache 服务器');
		}
		// 设置默认的服务器配置
		$this->_defaultConfig = array(
			'host' => 'localhost',
			'port' => 11211,
			'persistent' => false,
			'weight' => 1,
			'timeout' => 1,
			'retry_interval' => 15,
			'status' => true,
			'instant_death' => true,
			'failure_callback' => array($this, '_failedRequest'),
		);
		// 将 memcache 服务器添加到连接池中
		foreach( $servers as $server )
		{
			// 自定义配置与默认配置合并
			$server += $this->_defaultConfig;
			if ( !$this->_memcache->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retry_interval'], $server['status'], $server['failure_callback']) )
			{
				throw new ExceptionHandler('Memcache 无法连接到主机 \'' . $server['host'] . '\'，端口 \'' . $server['port']);
			}
		}
		// 设置标志
		$this->_flags = isset($this->_config['compression']) && $this->_config['compression'] === true ? MEMCACHE_COMPRESSED : false;
	}
	/**
	 * 根据 id 获取缓存值条目。
	 *
	 *     // 从 memcache 分组获取缓存条目
	 *     $data = Cache::instance('memcache')->get('foo');
	 *
	 * @param	string	$id 缓存条目的id
	 * @return	mixed
	 * @throws ExceptionHandler
	 */
	public function get($id)
	{
		// 从 Memcache 中取值
		$value = $this->_memcache->get($this->_sanitize_id($id));
		// 如果未找到该值，将其规范化
		if ( $value === false )
		{
			$value = NULL;
		}
		// 返回该值
		return $value;
	}
	/**
	 * 用 id 和到期时间给缓存设值。
	 *
	 *     $data = 'bar';
	 *
	 *     // 将 'bar' 置给 memcache 分组中的 'foo'，使用默认期限
	 * 	   Cache::instance('memcache')->set('foo', $data);
	 *
	 *     // 将 'bar' 置给 memcache 分组中的 'foo'，10分钟过期
	 *     Cache::instance('memcache')->set('foo', $data, 600);
	 *
	 * @param	string	$id 缓存条目的id
	 * @param mixed $data 置给缓存的数据
	 * @param	integer	$lifetime 以“秒”为单位的到期时间，最大值为 2592000
	 * @return	boolean
	 */
	public function set($id, $data, $lifetime = 3600)
	{
		// 如果到期时间大于上限
		if ( $lifetime > Cache_Memcache::CACHE_CEILING )
		{
			// 到期时间设置为最大缓存时间
			$lifetime = Cache_Memcache::CACHE_CEILING + time();
		}
		// 如果到期时间大于零
		elseif ( $lifetime > 0 )
		{
			$lifetime += time();
		}
		// 其它情况
		else
		{
			// 规范化到期时间
			$lifetime = 0;
		}
		// 将数据置给 memcache
		return $this->_memcache->set($this->_sanitize_id($id), $data, $this->_flags, $lifetime);
	}
	/**
	 * 根据 id 删除缓存条目。
	 *
	 *     // 立即删除 'foo' 条目
	 *     Cache::instance('memcache')->delete('foo');
	 *
	 *     // 30秒后删除 'bar' 条目
	 *     Cache::instance('memcache')->delete('bar', 30);
	 *
	 * @param	string	$id 要删除的条目id
	 * @param	integer	$timeout 条目超时，为零则立即删除，否则在规定的秒数后删除
	 * @return	boolean
	 */
	public function delete($id, $timeout = 0)
	{
		// 删除这个id
		return $this->_memcache->delete($this->_sanitize_id($id), $timeout);
	}
	/**
	 * 删除全部缓存条目。
	 *
	 * 在使用共享内存的缓存系统时，请谨慎使用这个方法，它会清除系统内所有客户端的每个条目。
	 *
	 *     // 删除 memcache 分组的全部缓存条目
	 *     Cache::instance('memcache')->deleteAll();
	 *
	 * @return	boolean
	 */
	public function deleteAll()
	{
		$result = $this->_memcache->flush();
		// 刷新以后必须延缓，否则不会覆盖！
		// 参阅 http://php.net/manual/zh/memcache.flush.php#81420
		sleep(1);
		return $result;
	}
	/**
	 * Memcache::failure_callback 的回调方法，用于特定服务器失效时的 Memcache 调用。
	 * 如果配置 `instant_death` 设置为 `true`，那么此方法将关掉这个服务器实例。
	 *
	 * @param	string	$hostname
	 * @param	integer	$port
	 * @return	void|boolean
	 */
	public function _failedRequest($hostname, $port)
	{
		if ( !$this->_config['instant_death'] )
			return;
		// 设置一个并不存在的主机
		$host = false;
		// 从配置中取得主机设置
		foreach( $this->_config['servers'] as $server )
		{
			// 与默认配置合并，因为它们不一定总被设置
			$server += $this->_defaultConfig;
			// 我们正在寻找发生故障的服务器
			if ( $hostname == $server['host'] && $port == $server['port'] )
			{
				// 要停用的服务器
				$host = $server;
				break;
			}
		}
		if ( !$host )
			return;
		else
		{
			return $this->_memcache->setServerParams(
					$host['host'], $host['port'], $host['timeout'], $host['retry_interval'], false, // 服务器离线
					array($this, '_failedRequest')
			);
		}
	}
}