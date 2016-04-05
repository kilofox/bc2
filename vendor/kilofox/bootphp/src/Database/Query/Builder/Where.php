<?php

namespace Bootphp\Database\Query\Builder;
use Bootphp\Database\Query\Builder;
/**
 * Database query builder for WHERE statements. See [Query Builder](/database/query/builder) for usage and examples.
 *
 * @package	   Kohana/Database
 * @category	  Query
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Where extends Builder
{
	// WHERE ...
	protected $_where = array();
	// ORDER BY ...
	protected $_order_by = array();
	// LIMIT ...
	protected $_limit = NULL;
	/**
	 * Creates a new "AND WHERE" condition for the query.
	 *
	 * @param mixed $column column name or array($column, $alias) or object
	 * @param	string	$op logic operator
	 * @param mixed $value column value
	 * @return	$this
	 */
	public function where($column, $op, $value)
	{
		$this->_where[] = array('AND' => array($column, $op, $value));
		return $this;
	}
	/**
	 * Creates a new "OR WHERE" condition for the query.
	 *
	 * @param mixed $column column name or array($column, $alias) or object
	 * @param	string	$op logic operator
	 * @param mixed $value column value
	 * @return	$this
	 */
	public function orWhere($column, $op, $value)
	{
		$this->_where[] = array('OR' => array($column, $op, $value));
		return $this;
	}
	/**
	 * Opens a new "AND WHERE (...)" grouping.
	 *
	 * @return	$this
	 */
	public function whereOpen()
	{
		$this->_where[] = array('AND' => '(');
		return $this;
	}
	/**
	 * Opens a new "OR WHERE (...)" grouping.
	 *
	 * @return	$this
	 */
	public function orWhereOpen()
	{
		$this->_where[] = array('OR' => '(');
		return $this;
	}
	/**
	 * Closes an open "WHERE (...)" grouping.
	 *
	 * @return	$this
	 */
	public function whereClose()
	{
		$this->_where[] = array('AND' => ')');
		return $this;
	}
	/**
	 * Closes an open "WHERE (...)" grouping.
	 *
	 * @return	$this
	 */
	public function orWhereClose()
	{
		$this->_where[] = array('OR' => ')');
		return $this;
	}
	/**
	 * Applies sorting with "ORDER BY ..."
	 *
	 * @param mixed $column column name or array($column, $alias) or object
	 * @param	string	$direction direction of sorting
	 * @return	$this
	 */
	public function orderBy($column, $direction = NULL)
	{
		$this->_order_by[] = array($column, $direction);
		return $this;
	}
	/**
	 * Return up to "LIMIT ..." results
	 *
	 * @param	integer	$number maximum results to return or NULL to reset
	 * @return	$this
	 */
	public function limit($number)
	{
		$this->_limit = ($number === NULL) ? NULL : (int)$number;
		return $this;
	}
}