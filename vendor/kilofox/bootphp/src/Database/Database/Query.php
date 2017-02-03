<?php

namespace Bootphp\Database\Database;

use Bootphp\Database\Database;
use Bootphp\Core;

/**
 * Database query wrapper. See [Parameterized Statements](database/query/parameterized) for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Query
{
    /**
     * Query type.
     *
     * @var string
     */
    protected $type;

    /**
     * Execute the query during a cache hit.
     *
     * @var boolean
     */
    protected $forceExecute = false;

    /**
     * Cache lifetime.
     *
     * @var mixed
     */
    protected $lifetime = null;

    /**
     * SQL statement.
     *
     * @var string
     */
    protected $_sql;

    /**
     * Quoted query parameters.
     *
     * @var array
     */
    protected $_parameters = [];

    /**
     * Return results as associative arrays or objects.
     *
     * @var boolean
     */
    protected $_as_object = false;

    /**
     * Parameters for __construct when using object results.
     *
     * @var array
     */
    protected $_object_params = [];

    /**
     * Creates a new SQL query of the specified type.
     *
     * @param   integer  $type  Query type: 'select', 'insert', etc
     * @param   string   $sql   Query string
     * @return  void
     */
    public function __construct($type, $sql)
    {
        $this->type = $type;
        $this->_sql = $sql;
    }

    /**
     * Return the SQL query string.
     *
     * @return  string
     */
    public function __toString()
    {
        try {
            // Return the SQL string
            return $this->compile(Database::instance());
        } catch (\Exception $e) {
            return \Bootphp\BootphpException::text($e);
        }
    }

    /**
     * Get the type of the query.
     *
     * @return  integer
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Enables the query to be cached for a specified amount of time.
     *
     * @param   integer  $lifetime  number of seconds to cache, 0 deletes it from the cache
     * @param   boolean  whether or not to execute the query during a cache hit
     * @return  $this
     * @uses    Core::$cacheLife
     */
    public function cached($lifetime = null, $force = false)
    {
        if ($lifetime === null) {
            // Use the global setting
            $lifetime = Core::$cacheLife;
        }

        $this->forceExecute = $force;
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * Returns results as associative arrays
     *
     * @return  $this
     */
    public function as_assoc()
    {
        $this->_as_object = false;

        $this->_object_params = [];

        return $this;
    }

    /**
     * Returns results as objects
     *
     * @param   string  $class  classname or true for stdClass
     * @param   array   $params
     * @return  $this
     */
    public function as_object($class = true, array $params = null)
    {
        $this->_as_object = $class;

        if ($params) {
            // Add object parameters
            $this->_object_params = $params;
        }

        return $this;
    }

    /**
     * Set the value of a parameter in the query.
     *
     * @param   string   $param  parameter key to replace
     * @param   mixed    $value  value to use
     * @return  $this
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
     * @param   string  $param  parameter key to replace
     * @param   mixed   $var    variable to use
     * @return  $this
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
     * @param   array  $params  list of parameters
     * @return  $this
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
     * @param   mixed  $db  Database instance or name of instance
     * @return  string
     */
    public function compile($db = null)
    {
        if (!is_object($db)) {
            // Get the database instance
            $db = Database::instance($db);
        }

        // Import the SQL locally
        $sql = $this->_sql;

        if (!empty($this->_parameters)) {
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
     * @param   mixed    $db            Database instance or name of instance
     * @param   string   $as_object     Result object classname, true for stdClass or false for array
     * @param   array    $objectParams Result object constructor arguments
     * @return  object   Database_Result for SELECT queries
     * @return  mixed    The insert id for INSERT queries
     * @return  integer  Number of affected rows for all other queries
     */
    public function execute($db = null, $as_object = null, $objectParams = null)
    {
        if (!is_object($db)) {
            // Get the database instance
            $db = Database::instance($db);
        }

        if ($as_object === null) {
            $as_object = $this->_as_object;
        }

        if ($objectParams === null) {
            $objectParams = $this->_object_params;
        }

        // Compile the SQL query
        $sql = $this->compile($db);

        if ($this->lifetime !== null AND $this->type === 'select') {
            // Set the cache key based on the database instance name and SQL
            $cacheKey = 'Database::query("' . $db . '", "' . $sql . '")';

            // Read the cache first to delete a possible hit with lifetime <= 0
            if (($result = Core::cache($cacheKey, null, $this->lifetime)) !== null AND ! $this->forceExecute) {
                // Return a cached result
                return new Database_Result_Cached($result, $sql, $as_object, $objectParams);
            }
        }

        // Execute the query
        $result = $db->query($this->type, $sql, $as_object, $objectParams);

        if (isset($cacheKey) AND $this->lifetime > 0) {
            // Cache the result array
            Core::cache($cacheKey, $result->as_array(), $this->lifetime);
        }

        return $result;
    }

}
