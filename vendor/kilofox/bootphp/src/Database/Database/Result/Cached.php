<?php

namespace Bootphp\Database\Database\Result;

/**
 * Object used for caching the results of select queries. See [Results](/database/results#select-cached)
 * for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query/Result
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Cached extends \Bootphp\Database\Database\Result
{
    public function __construct(array $result, $sql, $asObject = null)
    {
        parent::__construct($result, $sql, $asObject);

        // Find the number of rows in the result
        $this->totalRows = count($result);
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
        if ($this->offsetExists($offset)) {
            $this->currentRow = $offset;

            return true;
        }

        return false;
    }

    public function current()
    {
        // Return an array of the row
        return $this->valid() ? $this->result[$this->currentRow] : null;
    }

}
