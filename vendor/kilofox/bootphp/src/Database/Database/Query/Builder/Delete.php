<?php

namespace Bootphp\Database\Query\Builder;

/**
 * Database query builder for DELETE statements. See [Query Builder](/database/query/builder) for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Delete extends \Bootphp\Database\Query\Builder\Where
{
    /**
     * DELETE FROM ...
     *
     * @var string
     */
    protected $table;

    /**
     * Set the table for a delete.
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
        return parent::__construct('delete', '');
    }

    /**
     * Sets the table to delete from.
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
     * Compile the SQL query and return it.
     *
     * @param   mixed   $db     Database instance or name of instance
     * @return  string
     */
    public function compile($db = null)
    {
        if (!is_object($db)) {
            // Get the database instance
            $db = Database::instance($db);
        }

        // Start a deletion query
        $query = 'DELETE FROM ' . $db->quoteTable($this->table);

        if (!empty($this->_where)) {
            // Add deletion conditions
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
        $this->_where = [];

        $this->_parameters = [];

        $this->_sql = null;

        return $this;
    }

}
