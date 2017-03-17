<?php

namespace Bootphp\Database\Database;

/**
 * Database result wrapper. See [Results](/database/results) for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query/Result
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Result implements \Countable, \Iterator, \SeekableIterator, \ArrayAccess
{
    /**
     * Executed SQL for this result.
     *
     * @var     string
     */
    protected $query;

    /**
     * Raw result resource.
     *
     * @var     mixed
     */
    protected $result;

    /**
     * Total number of rows.
     *
     * @var     integer
     */
    protected $totalRows = 0;

    /**
     * Current row.
     *
     * @var     integer
     */
    protected $currentRow = 0;

    /**
     * Return rows as an object or associative array.
     *
     * @var     boolean
     */
    protected $asObject;

    /**
     * Sets the total number of rows and stores the result locally.
     *
     * @param   mixed   $result     Query result
     * @param   string  $sql        SQL query
     * @param   mixed   $asObject
     * @param   array   $params
     * @return  void
     */
    public function __construct($result, $sql, $asObject = true, array $params = null)
    {
        // Store the result locally
        $this->result = $result;

        // Store the SQL locally
        $this->query = $sql;

        // Results as objects or associative arrays
        $this->asObject = $asObject;
    }

    /**
     * Result destruction cleans up all open result sets.
     *
     * @return  void
     */
    abstract public function __destruct();
    /**
     * Get a cached database result from the current result iterator.
     *
     *     $cachable = serialize($result->cached());
     *
     * @return  Cached
     */
    public function cached()
    {
        return new \Bootphp\Database\Database\Result\Cached($this->asArray(), $this->query, $this->asObject);
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
     * @param   string  $key    Ccolumn for associative keys
     * @param   string  $value  Column for values
     * @return  array
     */
    public function asArray($key = null, $value = null)
    {
        $results = [];

        if ($key === null && $value === null) {
            // Indexed rows
            foreach ($this as $row) {
                $results[] = $row;
            }
        } elseif ($key === null) {
            // Indexed columns
            if ($this->asObject) {
                foreach ($this as $row) {
                    $results[] = $row->$value;
                }
            } else {
                foreach ($this as $row) {
                    $results[] = $row[$value];
                }
            }
        } elseif ($value === null) {
            // Associative rows
            if ($this->asObject) {
                foreach ($this as $row) {
                    $results[$row->$key] = $row;
                }
            } else {
                foreach ($this as $row) {
                    $results[$row[$key]] = $row;
                }
            }
        } else {
            // Associative columns
            if ($this->asObject) {
                foreach ($this as $row) {
                    $results[$row->$key] = $row->$value;
                }
            } else {
                foreach ($this as $row) {
                    $results[$row[$key]] = $row[$value];
                }
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
     * @param   string  $name       Column to get
     * @param   mixed   $default    Default value if the column does not exist
     * @return  mixed
     */
    public function get($name, $default = null)
    {
        $row = $this->current();

        if ($this->asObject) {
            if (isset($row->$name))
                return $row->$name;
        }
        else {
            if (isset($row[$name]))
                return $row[$name];
        }

        return $default;
    }

    /**
     * Implements [Countable::count], returns the total number of rows.
     *
     *     echo count($result);
     *
     * @return  integer
     */
    public function count()
    {
        return $this->totalRows;
    }

    /**
     * Implements [ArrayAccess::offsetExists], determines if row exists.
     *
     *     if (isset($result[10]))
     *     {
     *         // Row 10 exists
     *     }
     *
     * @param   integer $offset
     * @return  boolean
     */
    public function offsetExists($offset)
    {
        return $offset >= 0 && $offset < $this->totalRows;
    }

    /**
     * Implements [ArrayAccess::offsetGet], gets a given row.
     *
     *     $row = $result[10];
     *
     * @param   integer $offset
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        if (!$this->seek($offset))
            return null;

        return $this->current();
    }

    /**
     * Implements [ArrayAccess::offsetSet], throws an error.
     *
     * [!!] You cannot modify a database result.
     *
     * @param   integer $offset
     * @param   mixed   $value
     * @return  void
     * @throws  BootphpException
     */
    final public function offsetSet($offset, $value)
    {
        throw new BootphpException('Database results are read-only.');
    }

    /**
     * Implements [ArrayAccess::offsetUnset], throws an error.
     *
     * [!!] You cannot modify a database result.
     *
     * @param   integer $offset
     * @return  void
     * @throws  BootphpException
     */
    final public function offsetUnset($offset)
    {
        throw new BootphpException('Database results are read-only.');
    }

    /**
     * Implements [Iterator::key], returns the current row number.
     *
     *     echo key($result);
     *
     * @return  integer
     */
    public function key()
    {
        return $this->currentRow;
    }

    /**
     * Implements [Iterator::next], moves to the next row.
     *
     *     next($result);
     *
     * @return  $this
     */
    public function next()
    {
        ++$this->currentRow;

        return $this;
    }

    /**
     * Implements [Iterator::prev], moves to the previous row.
     *
     *     prev($result);
     *
     * @return  $this
     */
    public function prev()
    {
        --$this->currentRow;

        return $this;
    }

    /**
     * Implements [Iterator::rewind], sets the current row to zero.
     *
     *     rewind($result);
     *
     * @return  $this
     */
    public function rewind()
    {
        $this->currentRow = 0;

        return $this;
    }

    /**
     * Implements [Iterator::valid], checks if the current row exists.
     *
     * [!!] This method is only used internally.
     *
     * @return  boolean
     */
    public function valid()
    {
        return $this->offsetExists($this->currentRow);
    }

}
