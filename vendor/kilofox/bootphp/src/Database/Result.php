<?php

namespace Bootphp\Database;
/**
 * Database result wrapper. See [Results](/database/results) for usage and examples.
 *
 * @package	Bootphp/数据库
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
abstract class Result implements \Countable, \Iterator, \SeekableIterator, \ArrayAccess
{
	// Executed SQL for this result
	protected $_query;
	// Raw result resource
	protected $_result;
	// Total number of rows and current row
	protected $_total_rows = 0;
	protected $_current_row = 0;
	// Parameters for __construct when using object results
	protected $_object_params = NULL;
	/**
	 * Sets the total number of rows and stores the result locally.
	 *
	 * @param mixed $result query result
	 * @param	string	$sql SQL query
	 * @param boolean $asArray
	 * @param array $params
	 * @return	void
	 */
	public function __construct($result, $sql)
	{
		// Store the result locally
		$this->_result = $result;
		// Store the SQL locally
		$this->_query = $sql;
	}
	/**
	 * Result destruction cleans up all open result sets.
	 *
	 * @return	void
	 */
	abstract public function __destruct();
	/**
	 * Get a cached database result from the current result iterator.
	 *
	 *     $cachable = serialize($result->cached());
	 *
	 * @return	Database_Result_Cached
	 */
	public function cached()
	{
		return new ResultCached($this->asArray(), $this->_query);
	}
	/**
	 * Return all of the rows in the result as an array.
	 *
	 *     // Indexed array of all rows
	 *     $rows = $result->asArray();
	 *
	 *     // Associative array of rows by "id"
	 *     $rows = $result->asArray('id');
	 *
	 *     // Associative array of rows, "id" => "name"
	 *     $rows = $result->asArray('id', 'name');
	 *
	 * @param	string	$key column for associative keys
	 * @param	string	$value column for values
	 * @return	array
	 */
	public function asArray($key = NULL, $value = NULL)
	{
		$results = array();
		if ( $key === NULL && $value === NULL )
		{
			// 带索引的行
			foreach( $this as $row )
			{
				$results[] = $row;
			}
		}
		elseif ( $key === NULL )
		{
			// 带索引的列
			foreach( $this as $row )
			{
				$results[] = $row->$value;
			}
		}
		elseif ( $value === NULL )
		{
			// 关联行
			foreach( $this as $row )
			{
				$results[$row->$key] = $row;
			}
		}
		else
		{
			// 关联列
			foreach( $this as $row )
			{
				$results[$row->$key] = $row->$value;
			}
		}
		$this->rewind();
		return $results;
	}
	/**
	 * Return the named column from the current row.
	 *
	 *     // Get the "id" value
	 *     $id = $result->get('id');
	 *
	 * @param	string	$name column to get
	 * @param mixed $default default value if the column does not exist
	 * @return	mixed
	 */
	public function get($name = NULL, $default = NULL)
	{
		$row = $this->current();
		if ( $name === NULL )
			return $row;
		if ( isset($row->$name) )
			return $row->$name;
		return $default;
	}
	/**
	 * Implements [Countable::count], returns the total number of rows.
	 *
	 *     echo count($result);
	 *
	 * @return	integer
	 */
	public function count()
	{
		return $this->_total_rows;
	}
	/**
	 * Implements [ArrayAccess::offsetExists], determines if row exists.
	 *
	 *     if (isset($result[10]))
	 *     {
	 *         // Row 10 exists
	 *     }
	 *
	 * @param int $offset
	 * @return	boolean
	 */
	public function offsetExists($offset)
	{
		return ($offset >= 0 and $offset < $this->_total_rows);
	}
	/**
	 * Implements [ArrayAccess::offsetGet], gets a given row.
	 *
	 *     $row = $result[10];
	 *
	 * @param int $offset
	 * @return	mixed
	 */
	public function offsetGet($offset)
	{
		if ( !$this->seek($offset) )
			return NULL;
		return $this->current();
	}
	/**
	 * Implements [ArrayAccess::offsetSet], throws an error.
	 *
	 * [!!] You cannot modify a database result.
	 *
	 * @param int $offset
	 * @param mixed $value
	 * @return	void
	 * @throws Kohana_Exception
	 */
	final public function offsetSet($offset, $value)
	{
		throw new Kohana_Exception('Database results are read-only');
	}
	/**
	 * Implements [ArrayAccess::offsetUnset], throws an error.
	 *
	 * [!!] You cannot modify a database result.
	 *
	 * @param int $offset
	 * @return	void
	 * @throws Kohana_Exception
	 */
	final public function offsetUnset($offset)
	{
		throw new Kohana_Exception('Database results are read-only');
	}
	/**
	 * Implements [Iterator::key], returns the current row number.
	 *
	 *     echo key($result);
	 *
	 * @return	integer
	 */
	public function key()
	{
		return $this->_current_row;
	}
	/**
	 * Implements [Iterator::next], moves to the next row.
	 *
	 *     next($result);
	 *
	 * @return	$this
	 */
	public function next()
	{
		++$this->_current_row;
		return $this;
	}
	/**
	 * Implements [Iterator::prev], moves to the previous row.
	 *
	 *     prev($result);
	 *
	 * @return	$this
	 */
	public function prev()
	{
		--$this->_current_row;
		return $this;
	}
	/**
	 * Implements [Iterator::rewind], sets the current row to zero.
	 *
	 *     rewind($result);
	 *
	 * @return	$this
	 */
	public function rewind()
	{
		$this->_current_row = 0;
		return $this;
	}
	/**
	 * Implements [Iterator::valid], checks if the current row exists.
	 *
	 * [!!] This method is only used internally.
	 *
	 * @return	boolean
	 */
	public function valid()
	{
		return $this->offsetExists($this->_current_row);
	}
}