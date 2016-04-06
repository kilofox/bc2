<?php

namespace Bootphp\Cache;

use Bootphp\Cache\Cache;
use Bootphp\Exception\ExceptionHandler;

/**
 * [BootPHP Cache](api/Cache) APC driver. Provides an opcode based
 * driver for the Kohana Cache library.
 *
 * ### Configuration example
 *
 * Below is an example of an _apc_ server configuration.
 *
 *     return array(
 *         'apc' => array(			// apc group
 *             'driver' => 'apc',	// using APC driver
 *         ),
 *     );
 *
  In cases where only one cache group is required, if the group is named `default` there is
 * no need to pass the group name when instantiating a cache instance.
 *
 * #### General cache group configuration settings
 *
 * Name           | Required | Description
 * -------------- | -------- | ---------------------------------------------------------------
 * driver         | __YES__  | (_string_) The driver type to use
 *
 * ### System requirements
 *
 * *  APC PHP extension
 *
 * @package	BootPHP/Cache
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class CacheApc extends Cache {

	/**
	 * Check for existence of the APC extension This method cannot be invoked externally. The driver must
	 * be instantiated using the `Cache::instance()` method.
	 *
	 * @param	array	$config	configuration
	 * @throws	ExceptionHandler
	 */
	protected function __construct(array $config)
	{
		if ( !extension_loaded('apc') )
		{
			throw new Cache_Exception('PHP APC extension is not available.');
		}

		parent::__construct($config);
	}

	/**
	 * Retrieve a cached value entry by id.
	 *
	 *     // Retrieve cache entry from apc group
	 *     $data = Cache::instance('apc')->get('foo');
	 *
	 * @param	string	$id	id of cache to entry
	 * @return	mixed
	 * @throws ExceptionHandler
	 */
	public function get($id)
	{
		$data = apc_fetch($this->_sanitizeId($id), $success);

		return $success ? $data : NULL;
	}

	/**
	 * Set a value to cache with id and lifetime.
	 *
	 *     $data = 'bar';
	 *
	 *     // Set 'bar' to 'foo' in apc group, using default expiry
	 *     Cache::instance('apc')->set('foo', $data);
	 *
	 *     // Set 'bar' to 'foo' in apc group for 30 seconds
	 *     Cache::instance('apc')->set('foo', $data, 30);
	 *
	 * @param	string	$id			id of cache entry
	 * @param	string	$data		data to set to cache
	 * @param	integer	$lifetime	lifetime in seconds
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
	 * Delete a cache entry based on id.
	 *
	 *     // Delete 'foo' entry from the apc group
	 *     Cache::instance('apc')->delete('foo');
	 *
	 * @param	string	$id	id to remove from cache
	 * @return	boolean
	 */
	public function delete($id)
	{
		return apc_delete($this->_sanitizeId($id));
	}

	/**
	 * Delete all cache entries.
	 *
	 * Beware of using this method when using shared memory cache systems, as
	 * it will wipe every entry within the system for all clients.
	 *
	 *     // Delete all cache entries in the apc group
	 *     Cache::instance('apc')->deleteAll();
	 *
	 * @return	boolean
	 */
	public function deleteAll()
	{
		return apc_clear_cache('user');
	}
}