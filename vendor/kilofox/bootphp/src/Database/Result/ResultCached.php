<?php

namespace Bootphp\Database\Result;
use Bootphp\Database\Result;
/**
 * 用于缓存 select 查询结果的对象。用法与例子请参阅 [Results](/database/results#select-cached)。
 *
 * @package Bootphp/数据库
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class ResultCached extends Result
{
	public function __construct(array $result, $sql)
	{
		parent::__construct($result, $sql);
		// Find the number of rows in the result
		$this->_total_rows = count($result);
	}
	public function __destruct()
	{
		// Cached results do not use resources
	}
	public function cached()
	{
		return $this;
	}
	public function seek($offset)
	{
		if ( $this->offsetExists($offset) )
		{
			$this->_current_row = $offset;
			return true;
		}
		else
		{
			return false;
		}
	}
	public function current()
	{
		// Return an array of the row
		return $this->valid() ? $this->_result[$this->_current_row] : NULL;
	}
}