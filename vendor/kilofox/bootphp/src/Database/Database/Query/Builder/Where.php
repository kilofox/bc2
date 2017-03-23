<?php

namespace Bootphp\Database\Database\Query\Builder;

/**
 * Database query builder for WHERE statements. See [Query Builder](/database/query/builder) for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Where extends \Bootphp\Database\Database\Query\Builder
{
    /**
     * WHERE ...
     *
     * @var array
     */
    protected $where = [];

    /**
     * ORDER BY ...
     *
     * @var array
     */
    protected $orderBy = [];

    /**
     * LIMIT ...
     *
     * @var integer
     */
    protected $limit = null;

    /**
     * Creates a new "AND WHERE" condition for the query.
     *
     * @param   mixed   $column Column name or [$column, $alias] or object
     * @param   string  $op     Logic operator
     * @param   mixed   $value  Column value
     * @return  $this
     */
    public function where($column, $op, $value)
    {
        $this->where[] = ['AND' => [$column, $op, $value]];

        return $this;
    }

    /**
     * Creates a new "OR WHERE" condition for the query.
     *
     * @param   mixed   $column Column name or [$column, $alias] or object
     * @param   string  $op     Logic operator
     * @param   mixed   $value  Column value
     * @return  $this
     */
    public function orWhere($column, $op, $value)
    {
        $this->where[] = ['OR' => [$column, $op, $value]];

        return $this;
    }

    /**
     * Opens a new "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function whereOpen()
    {
        $this->where[] = ['AND' => '('];

        return $this;
    }

    /**
     * Opens a new "OR WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function orWhereOpen()
    {
        $this->where[] = ['OR' => '('];

        return $this;
    }

    /**
     * Closes an open "WHERE (...)" grouping or removes the grouping when it is empty.
     *
     * @return  $this
     */
    public function whereCloseEmpty()
    {
        $group = end($this->where);

        if ($group AND reset($group) === '(') {
            array_pop($this->where);

            return $this;
        }

        return $this->where_close();
    }

    /**
     * Closes an open "WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function whereClose()
    {
        $this->where[] = ['AND' => ')'];

        return $this;
    }

    /**
     * Closes an open "WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function orWhereClose()
    {
        $this->where[] = ['OR' => ')'];

        return $this;
    }

    /**
     * Applies sorting with "ORDER BY ...".
     *
     * @param   mixed   $column     Column name or [$column, $alias] or object
     * @param   string  $direction  Direction of sorting
     * @return  $this
     */
    public function orderBy($column, $direction = null)
    {
        $this->orderBy[] = [$column, $direction];

        return $this;
    }

    /**
     * Return up to "LIMIT ..." results.
     *
     * @param   integer $number     Maximum results to return or null to reset
     * @return  $this
     */
    public function limit($number)
    {
        $this->limit = $number === null ? null : (int) $number;

        return $this;
    }

}
