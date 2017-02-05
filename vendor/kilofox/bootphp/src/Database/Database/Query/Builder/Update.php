<?php

namespace Bootphp\Database\Database\Query\Builder;

/**
 * Database query builder for UPDATE statements. See [Query Builder](/database/query/builder) for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Update extends Where
{
    /**
     * UPDATE ...
     *
     * @var string
     */
    protected $table;

    /**
     * SET ...
     *
     * @var array
     */
    protected $set = [];

    /**
     * Set the table for a update.
     *
     * @param   mixed   $table  Table name or [$table, $alias] or object
     * @return  void
     */
    public function __construct($table = null)
    {
        if ($table) {
            // Set the inital table name
            $this->table = $table;
        }

        // Start the query with no SQL
        return parent::__construct('update', '');
    }

    /**
     * Sets the table to update.
     *
     * @param   mixed   $table  Table name or [$table, $alias] or object
     * @return  $this
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the values to update with an associative array.
     *
     * @param   array   $pairs  Associative (column => value) list
     * @return  $this
     */
    public function set(array $pairs)
    {
        foreach ($pairs as $column => $value) {
            $this->set[] = [$column, $value];
        }

        return $this;
    }

    /**
     * Set the value of a single column.
     *
     * @param   mixed  $column  table name or [$table, $alias] or object
     * @param   mixed  $value   column value
     * @return  $this
     */
    public function value($column, $value)
    {
        $this->set[] = [$column, $value];

        return $this;
    }

    /**
     * Compile the SQL query and return it.
     *
     * @param   mixed  $db  Database instance or name of instance
     * @return  string
     */
    public function compile($db = null)
    {
        if (!is_object($db)) {
            // Get the database instance
            $db = \Bootphp\Database\Database::instance($db);
        }

        // Start an update query
        $query = 'UPDATE ' . $db->quoteTable($this->table);

        // Add the columns to update
        $query .= ' SET ' . $this->compileSet($db, $this->set);

        if (!empty($this->_where)) {
            // Add selection conditions
            $query .= ' WHERE ' . $this->compileConditions($db, $this->_where);
        }

        if (!empty($this->_order_by)) {
            // Add sorting
            $query .= ' ' . $this->compileOrderBy($db, $this->_order_by);
        }

        if ($this->_limit !== null) {
            // Add limiting
            $query .= ' LIMIT ' . $this->_limit;
        }

        $this->_sql = $query;

        return parent::compile($db);
    }

    /**
     * Reset the current builder status.
     *
     * @return  $this
     */
    public function reset()
    {
        $this->table = null;

        $this->set = $this->_where = [];

        $this->_limit = null;

        $this->_parameters = [];

        $this->_sql = null;

        return $this;
    }

}
