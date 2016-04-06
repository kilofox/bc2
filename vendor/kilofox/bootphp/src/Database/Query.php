<?php

namespace Bootphp\Database;
use Bootphp\Database\Database;
use Bootphp\Database\Result\ResultCached;
use Bootphp\Cache\Cache;
/**
 * Database query wrapper.  See [Parameterized Statements](database/query/parameterized) for usage and examples.
 *
 * @package	Bootphp/Database
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class Query
{
	// Query type
	protected $_type;
	// Execute the query during a cache hit
	protected $_force_execute = false;
	// Cache lifetime
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
	 * Enables the query to be cached for a specified amount of time.
	 *
	 * @param   integer  $lifetime  number of seconds to cache, 0 deletes it from the cache
	 * @param   boolean  whether or not to execute the query during a cache hit
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
	 * Execute the current query on the given database.
	 *
	 * @param   mixed    $db  Database instance or name of instance
	 * @param   string   result object classname, TRUE for stdClass or FALSE for array
	 * @param   array    result object constructor arguments
	 * @return  object   Database_Result for SELECT queries
	 * @return  mixed    the insert id for INSERT queries
	 * @return  integer  number of affected rows for all other queries
	 */
	public function execute($db = NULL)
	{
		if ( !is_object($db) )
		{
			// Get the database instance
			$db = Database::instance($db);
		}

		// Compile the SQL query
		$sql = $this->compile($db);

		if ( $this->_lifetime !== NULL && $this->_type === 'select' )
		{
			// Set the cache key based on the database instance name and SQL
			$cacheKey = 'db_' . $db . '_' . $sql;

			// Read the cache first to delete a possible hit with lifetime <= 0
			if ( ($result = Cache::instance()->get($cacheKey)) !== NULL && !$this->_force_execute )
			{
				// Return a cached result
				return new ResultCached($result, $sql);
			}
		}

		// Execute the query
		$result = $db->query($this->_type, $sql);

		if ( isset($cacheKey) && $this->_lifetime > 0 )
		{
			// Cache the result array
			Cache::instance()->set($cacheKey, $result->asArray(), $this->_lifetime);
		}

		return $result;
	}
}