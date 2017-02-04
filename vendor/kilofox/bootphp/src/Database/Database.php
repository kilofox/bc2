<?php

namespace Bootphp\Database;

use Bootphp\Database\Query;
use Bootphp\Database\Database\Expression;

/**
 * Database connection wrapper/helper.
 *
 * You may get a database instance using `Database::instance('name')` where
 * name is the [config](database/config) group.
 *
 * This class provides connection instance management via Database Drivers, as
 * well as quoting, escaping and other related functions. Querys are done using
 * [Database\Query] and [Database\Query_Builder] objects, which can be easily
 * created using the [DB] helper class.
 *
 * @package    Bootphp/Database
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Database
{
    /**
     * @var string  Default instance name
     */
    public static $default = 'default';

    /**
     * @var array   Database instances
     */
    public static $instances = [];

    /**
     * Get a singleton Database instance. If configuration is not specified,
     * it will be loaded from the database configuration file using the same
     * group as the name.
     *
     *     // Load the default database
     *     $db = Database::instance();
     *
     *     // Create a custom configured instance
     *     $db = Database::instance('custom', $config);
     *
     * @param   string  $name   Instance name
     * @param   array   $config Configuration parameters
     * @return  Database
     */
    public static function instance($name = null, array $config = null)
    {
        if ($name === null) {
            // Use the default instance name
            $name = Database::$default;
        }

        if (!isset(Database::$instances[$name])) {
            if ($config === null) {
                // Load the configuration for this database
                $config = \Bootphp\Core::$config->load('database');
                $config = isset($config[$name]) ? $config[$name] : [];
            }

            if (!isset($config['type'])) {
                throw new \Bootphp\BootphpException('Database type not defined in ' . $name . ' configuration');
            }

            // Set the driver class name
            $driver = 'Bootphp\\Database\\Database\\Pdo' . ucfirst($config['type']);

            // Create the database connection instance
            $driver = new $driver($name, $config);

            // Store the database instance
            Database::$instances[$name] = $driver;
        }

        return Database::$instances[$name];
    }

    /**
     * @var  string  the last query executed
     */
    public $last_query;
    // Character that is used to quote identifiers
    protected $_identifier = '"';
    // Instance name
    protected $_instance;
    // Raw server connection
    protected $_connection;
    // Configuration array
    protected $_config;

    /**
     * Stores the database configuration locally and name the instance.
     *
     * [!!] This method cannot be accessed directly, you must use [Database::instance].
     *
     * @return  void
     */
    public function __construct($name, array $config)
    {
        // Set the instance name
        $this->_instance = $name;

        // Store the config locally
        $this->_config = $config;

        if (empty($this->_config['tablePrefix'])) {
            $this->_config['tablePrefix'] = '';
        }
    }

    /**
     * Disconnect from the database when the object is destroyed.
     *
     *     // Destroy the database instance
     *     unset(Database::instances[(string) $db], $db);
     *
     * [!!] Calling `unset($db)` is not enough to destroy the database, as it
     * will still be stored in `Database::$instances`.
     *
     * @return  void
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Returns the database instance name.
     *
     *     echo (string) $db;
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->_instance;
    }

    /**
     * Connect to the database. This is called automatically when the first
     * query is executed.
     *
     *     $db->connect();
     *
     * @throws  BootphpException
     * @return  void
     */
    abstract public function connect();
    /**
     * Disconnect from the database. This is called automatically by [Database::__destruct].
     * Clears the database instance from [Database::$instances].
     *
     *     $db->disconnect();
     *
     * @return  boolean
     */
    public function disconnect()
    {
        unset(Database::$instances[$this->_instance]);

        return true;
    }

    /**
     * Set the connection character set. This is called automatically by [Database::connect].
     *
     *     $db->set_charset('utf8');
     *
     * @param   string   $charset   Character set name
     * @return  void
     * @throws  BootphpException
     */
    abstract public function set_charset($charset);
    /**
     * Perform an SQL query of the given type.
     *
     *     // Make a SELECT query and use objects for results
     *     $db->query('select, 'SELECT * FROM groups', true);
     *
     *     // Make a SELECT query and use "Model_User" for the results
     *     $db->query(’select', 'SELECT * FROM users LIMIT 1', 'Model_User');
     *
     * @param   integer $type      'select', 'insert', etc
     * @param   string  $sql       SQL query
     * @param   mixed   $as_object Result object class string, true for stdClass, false for assoc array
     * @param   array   $params    Object construct parameters for result class
     * @return  object  Database\Result for SELECT queries
     * @return  array   List (insert id, row count) for INSERT queries
     * @return  integer Number of affected rows for all other queries
     */
    abstract public function query($type, $sql, $as_object = false, array $params = null);
    /**
     * Start a SQL transaction.
     *
     *     // Start the transactions
     *     $db->begin();
     *
     *     try {
     *          DB::insert('users')->values($user1)...
     *          DB::insert('users')->values($user2)...
     *          // Insert successful commit the changes
     *          $db->commit();
     *     }
     *     catch (BootphpException $e)
     *     {
     *          // Insert failed. Rolling back changes...
     *          $db->rollback();
     *      }
     *
     * @param   string  $mode   Transaction mode
     * @return  boolean
     */
    abstract public function begin($mode = null);
    /**
     * Commit the current transaction
     *
     *     // Commit the database changes
     *     $db->commit();
     *
     * @return  boolean
     */
    abstract public function commit();
    /**
     * Abort the current transaction
     *
     *     // Undo the changes
     *     $db->rollback();
     *
     * @return  boolean
     */
    abstract public function rollback();
    /**
     * Count the number of records in a table.
     *
     *     // Get the total number of records in the "users" table
     *     $count = $db->count_records('users');
     *
     * @param   mixed    $table  table name string or array(query, alias)
     * @return  integer
     */
    public function count_records($table)
    {
        // Quote the table name
        $table = $this->quote_table($table);

        return $this->query('select', 'SELECT COUNT(*) AS total_row_count FROM ' . $table, false)
                        ->get('total_row_count');
    }

    /**
     * List all of the tables in the database. Optionally, a LIKE string can
     * be used to search for specific tables.
     *
     *     // Get all tables in the current database
     *     $tables = $db->list_tables();
     *
     *     // Get all user-related tables
     *     $tables = $db->list_tables('user%');
     *
     * @param   string  $like   Table to search for
     * @return  array
     */
    abstract public function list_tables($like = null);
    /**
     * Lists all of the columns in a table.
     *
     *     // Get all columns from the "users" table
     *     $columns = $db->list_columns('users');
     *
     *     // Get the columns from a table that doesn't use the table prefix
     *     $columns = $db->list_columns('users', null, false);
     *
     * @param   string  $table      Table to get columns from
     * @param   boolean $addPrefix  Whether to add the table prefix automatically or not
     * @return  array
     */
    abstract public function listColumns($table, $addPrefix = true);
    /**
     * Return the table prefix defined in the current configuration.
     *
     *     $prefix = $db->table_prefix();
     *
     * @return  string
     */
    public function table_prefix()
    {
        return $this->_config['tablePrefix'];
    }

    /**
     * Quote a value for an SQL query.
     *
     *     $db->quote(null);   // 'null'
     *     $db->quote(10);     // 10
     *     $db->quote('fred'); // 'fred'
     *
     * Objects passed to this function will be converted to strings.
     * [Expression] objects will be compiled.
     * [Database\Query] objects will be compiled and converted to a sub-query.
     * All other objects will be converted using the `__toString` method.
     *
     * @param   mixed   $value  Any value to quote
     * @return  string
     * @uses    Database::escape
     */
    public function quote($value)
    {
        if ($value === null) {
            return 'null';
        } elseif ($value === true) {
            return "'1'";
        } elseif ($value === false) {
            return "'0'";
        } elseif (is_object($value)) {
            if ($value instanceof Query) {
                // Create a sub-query
                return '(' . $value->compile($this) . ')';
            } elseif ($value instanceof Expression) {
                // Compile the expression
                return $value->compile($this);
            } else {
                // Convert the object to a string
                return $this->quote((string) $value);
            }
        } elseif (is_array($value)) {
            return '(' . implode(', ', array_map(array($this, __FUNCTION__), $value)) . ')';
        } elseif (is_int($value)) {
            return (int) $value;
        } elseif (is_float($value)) {
            // Convert to non-locale aware float to prevent possible commas
            return sprintf('%F', $value);
        }

        return $this->escape($value);
    }

    /**
     * Quote a database column name and add the table prefix if needed.
     *
     *     $column = $db->quote_column($column);
     *
     * You can also use SQL methods within identifiers.
     *
     *     $column = $db->quote_column(DB::expr('COUNT(`column`)'));
     *
     * Objects passed to this function will be converted to strings.
     * [Expression] objects will be compiled.
     * [Database\Query] objects will be compiled and converted to a sub-query.
     * All other objects will be converted using the `__toString` method.
     *
     * @param   mixed   $column Column name or array(column, alias)
     * @return  string
     * @uses    Database::quote_identifier
     * @uses    Database::table_prefix
     */
    public function quote_column($column)
    {
        // Identifiers are escaped by repeating them
        $escaped_identifier = $this->_identifier . $this->_identifier;

        if (is_array($column)) {
            list($column, $alias) = $column;
            $alias = str_replace($this->_identifier, $escaped_identifier, $alias);
        }

        if ($column instanceof Query) {
            // Create a sub-query
            $column = '(' . $column->compile($this) . ')';
        } elseif ($column instanceof Expression) {
            // Compile the expression
            $column = $column->compile($this);
        } else {
            // Convert to a string
            $column = (string) $column;

            $column = str_replace($this->_identifier, $escaped_identifier, $column);

            if ($column === '*') {
                return $column;
            } elseif (strpos($column, '.') !== false) {
                $parts = explode('.', $column);

                if ($prefix = $this->table_prefix()) {
                    // Get the offset of the table name, 2nd-to-last part
                    $offset = count($parts) - 2;

                    // Add the table prefix to the table name
                    $parts[$offset] = $prefix . $parts[$offset];
                }

                foreach ($parts as & $part) {
                    if ($part !== '*') {
                        // Quote each of the parts
                        $part = $this->_identifier . $part . $this->_identifier;
                    }
                }

                $column = implode('.', $parts);
            } else {
                $column = $this->_identifier . $column . $this->_identifier;
            }
        }

        if (isset($alias)) {
            $column .= ' AS ' . $this->_identifier . $alias . $this->_identifier;
        }

        return $column;
    }

    /**
     * Quote a database table name and adds the table prefix if needed.
     *
     *     $table = $db->quote_table($table);
     *
     * Objects passed to this function will be converted to strings.
     * [Expression] objects will be compiled.
     * [Database\Query] objects will be compiled and converted to a sub-query.
     * All other objects will be converted using the `__toString` method.
     *
     * @param   mixed   $table  Table name or array(table, alias)
     * @return  string
     * @uses    Database::quote_identifier
     * @uses    Database::table_prefix
     */
    public function quote_table($table)
    {
        // Identifiers are escaped by repeating them
        $escaped_identifier = $this->_identifier . $this->_identifier;

        if (is_array($table)) {
            list($table, $alias) = $table;
            $alias = str_replace($this->_identifier, $escaped_identifier, $alias);
        }

        if ($table instanceof Query) {
            // Create a sub-query
            $table = '(' . $table->compile($this) . ')';
        } elseif ($table instanceof Expression) {
            // Compile the expression
            $table = $table->compile($this);
        } else {
            // Convert to a string
            $table = (string) $table;

            $table = str_replace($this->_identifier, $escaped_identifier, $table);

            if (strpos($table, '.') !== false) {
                $parts = explode('.', $table);

                if ($prefix = $this->table_prefix()) {
                    // Get the offset of the table name, last part
                    $offset = count($parts) - 1;

                    // Add the table prefix to the table name
                    $parts[$offset] = $prefix . $parts[$offset];
                }

                foreach ($parts as & $part) {
                    // Quote each of the parts
                    $part = $this->_identifier . $part . $this->_identifier;
                }

                $table = implode('.', $parts);
            } else {
                // Add the table prefix
                $table = $this->_identifier . $this->table_prefix() . $table . $this->_identifier;
            }
        }

        if (isset($alias)) {
            // Attach table prefix to alias
            $table .= ' AS ' . $this->_identifier . $this->table_prefix() . $alias . $this->_identifier;
        }

        return $table;
    }

    /**
     * Quote a database identifier.
     *
     * Objects passed to this function will be converted to strings.
     * [Expression] objects will be compiled.
     * [Database\Query] objects will be compiled and converted to a sub-query.
     * All other objects will be converted using the `__toString` method.
     *
     * @param   mixed   $value  Any identifier
     * @return  string
     */
    public function quote_identifier($value)
    {
        // Identifiers are escaped by repeating them
        $escaped_identifier = $this->_identifier . $this->_identifier;

        if (is_array($value)) {
            list($value, $alias) = $value;
            $alias = str_replace($this->_identifier, $escaped_identifier, $alias);
        }

        if ($value instanceof Database\Query) {
            // Create a sub-query
            $value = '(' . $value->compile($this) . ')';
        } elseif ($value instanceof Expression) {
            // Compile the expression
            $value = $value->compile($this);
        } else {
            // Convert to a string
            $value = (string) $value;

            $value = str_replace($this->_identifier, $escaped_identifier, $value);

            if (strpos($value, '.') !== false) {
                $parts = explode('.', $value);

                foreach ($parts as & $part) {
                    // Quote each of the parts
                    $part = $this->_identifier . $part . $this->_identifier;
                }

                $value = implode('.', $parts);
            } else {
                $value = $this->_identifier . $value . $this->_identifier;
            }
        }

        if (isset($alias)) {
            $value .= ' AS ' . $this->_identifier . $alias . $this->_identifier;
        }

        return $value;
    }

    /**
     * Sanitize a string by escaping characters that could cause an SQL injection attack.
     *
     *     $value = $db->escape('any string');
     *
     * @param   string  $value  Value to quote
     * @return  string
     */
    abstract public function escape($value);
}
