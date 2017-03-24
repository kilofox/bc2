<?php

namespace Bootphp\Cache;

/**
 * Kohana Cache provides a common interface to a variety of caching engines.
 * Tags are supported where available natively to the cache system. Bootphp
 * Cache supports multiple instances of cache engines through a grouped
 * singleton pattern.
 *
 * ### Supported cache engines
 *
 * *  [APC](http://php.net/manual/en/book.apc.php)
 * *  [eAccelerator](http://eaccelerator.net/)
 * *  File
 * *  [Memcache](http://memcached.org/)
 * *  [Memcached-tags](http://code.google.com/p/memcached-tags/)
 * *  [SQLite](http://www.sqlite.org/)
 * *  [Xcache](http://xcache.lighttpd.net/)
 *
 * ### Introduction to caching
 *
 * Caching should be implemented with consideration. Generally, caching the
 * result of resources is faster than reprocessing them. Choosing what, how and
 * when to cache is vital. PHP APC is presently one of the fastest caching
 * systems available, closely followed by Memcache. SQLite and File caching are
 * two of the slowest cache methods, however usually faster than reprocessing a
 * complex set of instructions.
 *
 * Caching engines that use memory are considerably faster than the file based
 * alternatives. But memory is limited whereas disk space is plentiful. If
 * caching large datasets it is best to use file caching.
 *
 * ### Configuration settings
 *
 * Kohana Cache uses configuration groups to create cache instances. A
 * configuration group can use any supported driver, with successive groups
 * using the same driver type if required.
 *
 * #### Configuration example
 *
 * Below is an example of a _memcache_ server configuration.
 *
 *     return [
 *         // Name of group
 *         'memcache' => [
 *             // using Memcache driver
 *             'driver' => 'memcache',
 *             // Available server definitions
 *             'servers' => [
 *                 [
 *                     'host' => 'localhost',
 *                     'port' => 11211,
 *                     'persistent' => false
 *                 ]
 *             ],
 *             // Use compression?
 *             'compression' => false,
 *         ],
 *     ];
 *
 * In cases where only one cache group is required, set `Cache::$default` (in
 * your bootstrap, or by extending `Kohana_Cache` class) to the name of the group, and use:
 *
 *     $cache = Cache::instance(); // instead of Cache::instance('memcache')
 *
 * It will return the cache instance of the group it has been set in `Cache::$default`.
 *
 * #### General cache group configuration settings
 *
 * Below are the settings available to all types of cache driver.
 *
 * Name           | Required | Description
 * -------------- | -------- | ---------------------------------------------------------------
 * driver         | __YES__  | (_string_) The driver type to use
 *
 * Details of the settings specific to each driver are available within the drivers documentation.
 *
 * @package    Bootphp/Cache
 * @category   Base
 * @version    2.0
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Cache
{
    const DEFAULT_EXPIRE = 3600;

    /**
     * Default driver to use.
     *
     * @var string
     */
    public static $default = 'file';

    /**
     * Cache instances.
     *
     * @var Cache
     */
    public static $instances = [];

    /**
     * Creates a singleton of a Kohana Cache group. If no group is supplied
     * the __default__ cache group is used.
     *
     *     // Create an instance of the default group
     *     $default_group = Cache::instance();
     *
     *     // Create an instance of a group
     *     $foo_group = Cache::instance('foo');
     *
     *     // Access an instantiated group directly
     *     $foo_group = Cache::$instances['default'];
     *
     * @param   string  $group  The name of the cache group to use [Optional]
     * @return  Cache
     * @throws  \Bootphp\BootphpException
     */
    public static function instance($group = null)
    {
        // If there is no group supplied
        if ($group === null) {
            // Use the default setting
            $group = Cache::$default;
        }

        if (isset(Cache::$instances[$group])) {
            // Return the current group if initiated already
            return Cache::$instances[$group];
        }

        $config = \Bootphp\Core::$config->load('cache');

        if (!isset($config[$group])) {
            throw new \Bootphp\BootphpException('Failed to load Bootphp Cache group: ' . $group . '.');
        }

        $config = $config[$group];

        // Create a new cache type instance
        $cacheClass = 'Bootphp\\Cache\\Driver\\' . ucfirst($config['driver']) . 'Driver';
        Cache::$instances[$group] = new $cacheClass($config);

        // Return the instance
        return Cache::$instances[$group];
    }

    /**
     * @var Config
     */
    protected $_config = [];

    /**
     * Ensures singleton pattern is observed, loads the default expiry.
     *
     * @param   array   $config Configuration
     */
    protected function __construct(array $config)
    {
        $this->config($config);
    }

    /**
     * Getter and setter for the configuration. If no argument provided, the
     * current configuration is returned. Otherwise the configuration is set to
     * this class.
     *
     *     // Overwrite all configuration
     *     $cache->config(['driver' => 'memcache', '...']);
     *
     *     // Set a new configuration setting
     *      $cache->config('servers', [
     *          'foo' => 'bar',
     *          '...'
     *      ]);
     *
     *     // Get a configuration setting
     *     $servers = $cache->config('servers);
     *
     * @param   mixed   $key    Key to set to array, either array or config path
     * @param   mixed   $value  Value to associate with key
     * @return  mixed
     */
    public function config($key = null, $value = null)
    {
        if ($key === null)
            return $this->_config;

        if (is_array($key)) {
            $this->_config = $key;
        } else {
            if ($value === null)
                return isset($this->_config[$key]) ? $this->_config[$key] : null;

            $this->_config[$key] = $value;
        }

        return $this;
    }

    /**
     * Overload the __clone() method to prevent cloning.
     *
     * @return  void
     * @throws  \Bootphp\BootphpException
     */
    final public function __clone()
    {
        throw new \Bootphp\BootphpException('Cloning of Boophp\Cache objects is forbidden.');
    }

    /**
     * Retrieve a cached value entry by id.
     *
     *     // Retrieve cache entry from default group
     *     $data = Cache::instance()->get('foo');
     *
     *     // Retrieve cache entry from default group and return 'bar' if miss
     *     $data = Cache::instance()->get('foo', 'bar');
     *
     *     // Retrieve cache entry from memcache group
     *     $data = Cache::instance('memcache')->get('foo');
     *
     * @param   string  $id         Id of cache to entry
     * @param   string  $default    Default value to return if cache miss
     * @return  mixed
     * @throws  \Bootphp\BootphpException
     */
    abstract public function get($id, $default = null);
    /**
     * Set a value to cache with id and lifetime.
     *
     *     $data = 'bar';
     *
     *     // Set 'bar' to 'foo' in default group, using default expiry
     *     Cache::instance()->set('foo', $data);
     *
     *     // Set 'bar' to 'foo' in default group for 30 seconds
     *     Cache::instance()->set('foo', $data, 30);
     *
     *     // Set 'bar' to 'foo' in memcache group for 10 minutes
     *     if (Cache::instance('memcache')->set('foo', $data, 600)) {
     *          // Cache was set successfully
     *          return;
     *     }
     *
     * @param   string  $id         Id of cache entry
     * @param   string  $data       Data to set to cache
     * @param   integer $lifetime   Lifetime in seconds
     * @return  boolean
     */
    abstract public function set($id, $data, $lifetime = 3600);
    /**
     * Delete a cache entry based on id.
     *
     *     // Delete 'foo' entry from the default group
     *     Cache::instance()->delete('foo');
     *
     *     // Delete 'foo' entry from the memcache group
     *     Cache::instance('memcache')->delete('foo');
     *
     * @param   string  $id     Id to remove from cache
     * @return  boolean
     */
    abstract public function delete($id);
    /**
     * Delete all cache entries.
     *
     * Beware of using this method when using shared memory cache systems, as it
     * will wipe every entry within the system for all clients.
     *
     *     // Delete all cache entries in the default group
     *     Cache::instance()->deleteAll();
     *
     *     // Delete all cache entries in the memcache group
     *     Cache::instance('memcache')->deleteAll();
     *
     * @return  boolean
     */
    abstract public function deleteAll();
    /**
     * Replaces troublesome characters with underscores.
     *
     *     // Sanitize a cache id
     *     $id = $this->sanitizeId($id);
     *
     * @param   string  $id     Id of cache to sanitize
     * @return  string
     */
    protected function sanitizeId($id)
    {
        // Change slashes and spaces to underscores
        return str_replace(['/', '\\', ' '], '_', $id);
    }

}
