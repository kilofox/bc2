<?php

namespace Bootphp\Database;
use Bootphp\Database\Database;
use Bootphp\Database\Result\ResultCached;
use Bootphp\Cache\Cache;
/**
 * 数据库查询封装。用法与例子见请参阅 [Parameterized Statements](database/query/parameterized)。
 *
 * @package Bootphp/数据库
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class Query
{
	// Query type
	protected $_type;
	// 在碰到缓存时仍执行查询
	protected $_force_execute = false;
	// 缓存周期
	protected $_lifetime = NULL;
	// SQL statement
	protected $_sql;
	// Quoted query parameters
	protected $_parameters = array();
	// Parameters for __construct when using object results
	/**
	 * Creates a new SQL query of the specified type.
	 *
	 * @param	integer	$type query type: 'select', 'insert', etc
	 * @param	string	$sql query string
	 * @return	void
	 */
	public function __construct($type, $sql)
	{
		$this->_type = $type;
		$this->_sql = $sql;
	}
	/**
	 * Return the SQL query string.
	 *
	 * @return	string
	 */
	public function __toString()
	{
		try
		{
			// Return the SQL string
			return $this->compile(Database::instance());
		}
		catch( Exception $e )
		{
			return Kohana_Exception::text($e);
		}
	}
	/**
	 * Get the type of the query.
	 *
	 * @return	integer
	 */
	public function type()
	{
		return $this->_type;
	}
	/**
	 * 开启缓存指定时间量的查询。
	 *
	 * @param	integer	$lifetime 缓存的秒数，0为从缓存中删除它
	 * @param boolean 是否在碰到缓存时仍执行查询
	 * @return	$this
	 * @uses Kohana::$cache_life
	 */
	public function cached($lifetime = NULL, $force = false)
	{
		if ( $lifetime === NULL )
		{
			// Use the global setting
			//$lifetime = Kohana::$cache_life;
			$lifetime = 60;
		}
		$this->_force_execute = $force;
		$this->_lifetime = $lifetime;
		return $this;
	}
	/**
	 * Returns results as associative arrays
	 *
	 * @return	$this
	 */
	public function as_assoc()
	{
		return $this;
	}
	/**
	 * Returns results as objects
	 *
	 * @param	string	$class classname or true for stdClass
	 * @param array $params
	 * @return	$this
	 */
	public function as_object($class = true)
	{
		return $this;
	}
	/**
	 * Set the value of a parameter in the query.
	 *
	 * @param	string	$param parameter key to replace
	 * @param mixed $value value to use
	 * @return	$this
	 */
	public function param($param, $value)
	{
		// Add or overload a new parameter
		$this->_parameters[$param] = $value;
		return $this;
	}
	/**
	 * Bind a variable to a parameter in the query.
	 *
	 * @param	string	$param parameter key to replace
	 * @param mixed $var variable to use
	 * @return	$this
	 */
	public function bind($param, & $var)
	{
		// Bind a value to a variable
		$this->_parameters[$param] = & $var;
		return $this;
	}
	/**
	 * Add multiple parameters to the query.
	 *
	 * @param array $params list of parameters
	 * @return	$this
	 */
	public function parameters(array $params)
	{
		// Merge the new parameters in
		$this->_parameters = $params + $this->_parameters;
		return $this;
	}
	/**
	 * Compile the SQL query and return it. Replaces any parameters with their
	 * given values.
	 *
	 * @param mixed $db Database instance or name of instance
	 * @return	string
	 */
	public function compile($db = NULL)
	{
		if ( !is_object($db) )
		{
			// Get the database instance
			$db = Database::instance($db);
		}
		// Import the SQL locally
		$sql = $this->_sql;
		if ( !empty($this->_parameters) )
		{
			// Quote all of the values
			$values = array_map(array($db, 'quote'), $this->_parameters);
			// Replace the values in the SQL
			$sql = strtr($sql, $values);
		}
		return $sql;
	}
	/**
	 * 在给定的数据库上执行当前查询。
	 *
	 * @param mixed $db Database instance or name of instance
	 * @param  array    result object constructor arguments
	 * @return	object   Database_Result for SELECT queries
	 * @return	mixed    the insert id for INSERT queries
	 * @return	integer  number of affected rows for all other queries
	 */
	public function execute($db = NULL)
	{
		if ( !is_object($db) )
		{
			// 得到数据库实例
			$db = Database::instance($db);
		}
		// 编译 SQL 查询
		$sql = $this->compile($db);
		if ( $this->_lifetime !== NULL && $this->_type === 'select' )
		{
			// 设置基于数据库实例名和SQL的缓存键
			$cacheKey = 'db_' . $db . '_' . $sql;
			// Read the cache first to delete a possible hit with lifetime <= 0
			if ( ($result = Cache::instance()->get($cacheKey)) !== NULL && !$this->_force_execute )
			{
				// 返回缓存结果
				return new ResultCached($result, $sql);
			}
		}
		// 执行查询
		$result = $db->query($this->_type, $sql);
		if ( isset($cacheKey) && $this->_lifetime > 0 )
		{
			// 缓存结果数组
			Cache::instance()->set($cacheKey, $result->asArray(), $this->_lifetime);
		}
		return $result;
	}
}