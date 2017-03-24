<?php

namespace Bootphp\Database\Database;

use Bootphp\Database\Database;
use Bootphp\Core;
use Bootphp\Cache\Cache;

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
    protected $sql;

    /**
     * Quoted query parameters.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Return results as associative arrays or objects.
     *
     * @var boolean
     */
    protected $asObject = false;

    /**
     * Parameters for __construct when using object results.
     *
     * @var array
     */
    protected $objectParams = [];

    /**
     * Creates a new SQL query of the specified type.
     *
     * @param   integer  $type  Query type: 'select', 'insert', etc.
     * @param   string   $sql   Query string
     * @return  void
     */
    public function __construct($type, $sql)
    {
        $this->type = $type;
        $this->sql = $sql;
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
     * Set the value of a parameter in the query.
     *
     * @param   string   $param  parameter key to replace
     * @param   mixed    $value  value to use
     * @return  $this
     */
    public function param($param, $value)
    {
        // Add or overload a new parameter
        $this->parameters[$param] = $value;

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
        $this->parameters[$param] = & $var;

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
        $this->parameters = $params + $this->parameters;

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
        $sql = $this->sql;

        if (!empty($this->parameters)) {
            // Quote all of the values
            $values = array_map([$db, 'quote'], $this->parameters);

            // Replace the values in the SQL
            $sql = strtr($sql, $values);
        }

        return $sql;
    }

    /**
     * Execute the current query on the given database.
     *
     * @param   mixed   $db             Database instance or name of instance
     * @param   boolean $asObject       true for stdClass or false for array
     * @return  object  Database_Result for SELECT queries
     * @return  mixed   The insert id for INSERT queries
     * @return  integer Number of affected rows for all other queries
     */
    public function execute($db = null, $asObject = true)
    {
        if (!is_object($db)) {
            // Get the database instance
            $db = Database::instance($db);
        }

        // Compile the SQL query
        $sql = $this->compile($db);

        if ($this->lifetime !== null && $this->type === 'select') {
            // Set the cache key based on the database instance name and SQL
            $cacheKey = 'Database::query("' . $db . '", "' . $sql . '")';

            // Read the cache first to delete a possible hit with lifetime <= 0
            $result = Cache::instance($cacheKey, null, $this->lifetime);
            if ($result !== null && !$this->forceExecute) {
                // Return a cached result
                return new Result\Cached($result, $sql, $asObject);
            }
        }

        // Execute the query
        $result = $db->query($this->type, $sql, $asObject);

        if (isset($cacheKey) && $this->lifetime > 0) {
            // Cache the result array
            Core::cache($cacheKey, $result->asArray(), $this->lifetime);
        }

        return $result;
    }

}
