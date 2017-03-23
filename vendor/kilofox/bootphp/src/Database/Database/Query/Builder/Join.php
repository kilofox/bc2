<?php

namespace Bootphp\Database\Database\Query\Builder;

use Bootphp\BootphpException;

/**
 * Database query builder for JOIN statements. See [Query Builder](/database/query/builder) for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Join extends \Bootphp\Database\Database\Query\Builder
{
    /**
     * Type of JOIN.
     *
     * @var string
     */
    protected $type;

    /**
     * JOIN ...
     *
     * @var string
     */
    protected $table;

    /**
     * ON ...
     *
     * @var array
     */
    protected $on = [];

    /**
     * USING ...
     *
     * @var array
     */
    protected $using = [];

    /**
     * Creates a new JOIN statement for a table. Optionally, the type of JOIN
     * can be specified as the second parameter.
     *
     * @param   mixed   $table  Column name or [$column, $alias]
     * @param   string  $type   Type of JOIN: INNER, RIGHT, LEFT, etc
     * @return  void
     */
    public function __construct($table, $type = null)
    {
        // Set the table to JOIN on
        $this->table = $table;

        if ($type !== null) {
            // Set the JOIN type
            $this->type = (string) $type;
        }
    }

    /**
     * Adds a new condition for joining.
     *
     * @param   string  $c1     Column name
     * @param   string  $op     Logic operator
     * @param   string  $c2     Column name
     * @return  $this
     */
    public function on($c1, $op, $c2)
    {
        if (!empty($this->using)) {
            throw new BootphpException('JOIN ... ON ... cannot be combined with JOIN ... USING ...');
        }

        $this->on[] = [$c1, $op, $c2];

        return $this;
    }

    /**
     * Adds a new condition for joining.
     *
     * @param   string  $columns    Column name
     * @return  $this
     */
    public function using($columns)
    {
        if (!empty($this->on)) {
            throw new BootphpException('JOIN ... ON ... cannot be combined with JOIN ... USING ...');
        }

        $columns = func_get_args();

        $this->using = array_merge($this->using, $columns);

        return $this;
    }

    /**
     * Compile the SQL partial for a JOIN statement and return it.
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

        $sql = $this->type ? strtoupper($this->type) . ' JOIN' : 'JOIN';

        // Quote the table name that is being joined
        $sql .= ' ' . $db->quoteTable($this->table);

        if (!empty($this->using)) {
            // Quote and concat the columns
            $sql .= ' USING (' . implode(', ', array_map([$db, 'quoteColumn'], $this->using)) . ')';
        } else {
            $conditions = [];
            foreach ($this->on as $condition) {
                // Split the condition
                list($c1, $op, $c2) = $condition;

                if ($op) {
                    // Make the operator uppercase and spaced
                    $op = ' ' . strtoupper($op);
                }

                // Quote each of the columns used for the condition
                $conditions[] = $db->quoteColumn($c1) . $op . ' ' . $db->quoteColumn($c2);
            }

            // Concat the conditions "... AND ..."
            $sql .= ' ON (' . implode(' AND ', $conditions) . ')';
        }

        return $sql;
    }

    /**
     * Reset the current builder status.
     *
     * @return  $this
     */
    public function reset()
    {
        $this->type = $this->table = null;

        $this->on = [];
    }

}
