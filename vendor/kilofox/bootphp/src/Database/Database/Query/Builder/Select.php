<?php

namespace Bootphp\Database\Database\Query\Builder;

/**
 * Database query builder for SELECT statements. See [Query Builder](/database/query/builder) for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Select extends Where
{
    /**
     * SELECT ...
     *
     * @var array
     */
    protected $select = [];

    /**
     * DISTINCT
     * @var boolean
     */
    protected $distinct = false;

    /**
     * FROM ...
     *
     * @var array
     */
    protected $from = [];

    /**
     * JOIN ...
     *
     * @var array
     */
    protected $join = [];

    /**
     * GROUP BY ...
     *
     * @var array
     */
    protected $groupBy = [];

    /**
     * HAVING ...
     *
     * @var array
     */
    protected $having = [];

    /**
     * HAVING ...
     *
     * @var mixed
     */
    protected $offset = null;

    /**
     * UNION ...
     *
     * @var array
     */
    protected $union = [];

    /**
     * UNION ...
     *
     * @var Join
     */
    protected $lastJoin;

    /**
     * Sets the initial columns to select from.
     *
     * @param   array   $columns    Column list
     * @return  void
     */
    public function __construct(array $columns = null)
    {
        if (!empty($columns)) {
            // Set the initial columns
            $this->select = $columns;
        }

        // Start the query with no actual SQL statement
        parent::__construct('select', '');
    }

    /**
     * Enables or disables selecting only unique columns using "SELECT DISTINCT".
     *
     * @param   boolean $value      Enable or disable distinct columns
     * @return  $this
     */
    public function distinct($value)
    {
        $this->distinct = (bool) $value;

        return $this;
    }

    /**
     * Choose the columns to select from.
     *
     * @param   mixed   $columns    Column name or [$column, $alias] or object
     * @return  $this
     */
    public function select($columns = null)
    {
        $columns = func_get_args();

        $this->select = array_merge($this->select, $columns);

        return $this;
    }

    /**
     * Choose the columns to select from, using an array.
     *
     * @param   array   $columns    List of column names or aliases
     * @return  $this
     */
    public function selectArray(array $columns)
    {
        $this->select = array_merge($this->select, $columns);

        return $this;
    }

    /**
     * Choose the tables to select "FROM ...".
     *
     * @param   mixed   $table  Table name or [$table, $alias] or object
     * @return  $this
     */
    public function from($tables)
    {
        $tables = func_get_args();

        $this->from = array_merge($this->from, $tables);

        return $this;
    }

    /**
     * Adds addition tables to "JOIN ...".
     *
     * @param   mixed   $table  Column name or [$column, $alias] or object
     * @param   string  $type   Join type (LEFT, RIGHT, INNER, etc)
     * @return  $this
     */
    public function join($table, $type = null)
    {
        $this->join[] = $this->lastJoin = new Join($table, $type);

        return $this;
    }

    /**
     * Adds "ON ..." conditions for the last created JOIN statement.
     *
     * @param   mixed   $c1     Column name or [$column, $alias] or object
     * @param   string  $op     Logic operator
     * @param   mixed   $c2     Column name or [$column, $alias] or object
     * @return  $this
     */
    public function on($c1, $op, $c2)
    {
        $this->lastJoin->on($c1, $op, $c2);

        return $this;
    }

    /**
     * Adds "USING ..." conditions for the last created JOIN statement.
     *
     * @param   mixed  $columns    Column name
     * @return  $this
     */
    public function using($columns)
    {
        $columns = func_get_args();

        call_user_func_array([$this->lastJoin, 'using'], $columns);

        return $this;
    }

    /**
     * Creates a "GROUP BY ..." filter.
     *
     * @param   mixed   $columns    Column name or [$column, $alias] or object
     * @return  $this
     */
    public function groupBy($columns)
    {
        $columns = func_get_args();

        $this->groupBy = array_merge($this->groupBy, $columns);

        return $this;
    }

    /**
     * Creates a new "AND HAVING" condition for the query.
     *
     * @param   mixed   $column Column name or [$column, $alias] or object
     * @param   string  $op     Logic operator
     * @param   mixed   $value  Column value
     * @return  $this
     */
    public function having($column, $op, $value = null)
    {
        $this->having[] = ['AND' => [$column, $op, $value]];

        return $this;
    }

    /**
     * Creates a new "OR HAVING" condition for the query.
     *
     * @param   mixed   $column Column name or [$column, $alias] or object
     * @param   string  $op     Logic operator
     * @param   mixed   $value  Column value
     * @return  $this
     */
    public function orHaving($column, $op, $value = null)
    {
        $this->having[] = ['OR' => [$column, $op, $value]];

        return $this;
    }

    /**
     * Opens a new "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function havingOpen()
    {
        $this->having[] = ['AND' => '('];

        return $this;
    }

    /**
     * Opens a new "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function orHavingOpen()
    {
        $this->having[] = ['OR' => '('];

        return $this;
    }

    /**
     * Closes an open "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function havingClose()
    {
        $this->having[] = ['AND' => ')'];

        return $this;
    }

    /**
     * Closes an open "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function orHavingClose()
    {
        $this->having[] = ['OR' => ')'];

        return $this;
    }

    /**
     * Adds an other UNION clause.
     *
     * @param mixed $select  if string, it must be the name of a table. Else
     *  must be an instance of Database\Query\Builder\Select
     * @param boolean $all  decides if it's an UNION or UNION ALL clause
     * @return $this
     */
    public function union($select, $all = true)
    {
        if (is_string($select)) {
            $select = DB::select()->from($select);
        }

        if (!$select instanceof Select) {
            throw new \Bootphp\BootphpException('First parameter must be a string or an instance of Bootphp\Database\Database\Query\Builder\Select.');
        }

        $this->union [] = ['select' => $select, 'all' => $all];

        return $this;
    }

    /**
     * Start returning results after "OFFSET ...".
     *
     * @param   integer $number Starting result number or null to reset
     * @return  $this
     */
    public function offset($number)
    {
        $this->offset = $number === null ? null : (int) $number;

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
            $db = \Bootphp\Database::instance($db);
        }

        // Start a selection query
        $query = 'SELECT ';

        if ($this->distinct === true) {
            // Select only unique results
            $query .= 'DISTINCT ';
        }

        if (empty($this->select)) {
            // Select all columns
            $query .= '*';
        } else {
            // Select all columns
            $query .= implode(', ', array_unique(array_map([$db, 'quoteColumn'], $this->select)));
        }

        if (!empty($this->from)) {
            // Set tables to select from
            $query .= ' FROM ' . implode(', ', array_unique(array_map([$db, 'quoteTable'], $this->from)));
        }

        if (!empty($this->join)) {
            // Add tables to join
            $query .= ' ' . $this->compileJoin($db, $this->join);
        }

        if (!empty($this->where)) {
            // Add selection conditions
            $query .= ' WHERE ' . $this->compileConditions($db, $this->where);
        }

        if (!empty($this->groupBy)) {
            // Add grouping
            $query .= ' ' . $this->compileGroupBy($db, $this->groupBy);
        }

        if (!empty($this->having)) {
            // Add filtering conditions
            $query .= ' HAVING ' . $this->compileConditions($db, $this->having);
        }

        if (!empty($this->orderBy)) {
            // Add sorting
            $query .= ' ' . $this->compileOrderBy($db, $this->orderBy);
        }

        if ($this->limit !== null) {
            // Add limiting
            $query .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            // Add offsets
            $query .= ' OFFSET ' . $this->offset;
        }

        if (!empty($this->union)) {
            $query = '(' . $query . ')';
            foreach ($this->union as $u) {
                $query .= ' UNION ';
                if ($u['all'] === true) {
                    $query .= 'ALL ';
                }
                $query .= '(' . $u['select']->compile($db) . ')';
            }
        }

        $this->sql = $query;

        return parent::compile($db);
    }

    /**
     * Reset the current builder status.
     *
     * @return  $this
     */
    public function reset()
    {
        $this->select = $this->from = $this->join = $this->where = $this->groupBy = $this->having = $this->orderBy = $this->union = [];

        $this->distinct = false;

        $this->limit = $this->offset = $this->lastJoin = null;

        $this->parameters = [];

        $this->sql = null;

        return $this;
    }

}
