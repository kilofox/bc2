<?php

namespace Bootphp\Cache\Cache;

/**
 * Kohana Cache Arithmetic Interface, for basic cache integer based
 * arithmetic, addition and subtraction.
 *
 * @package    Bootphp/Cache
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 * @since      3.2.0
 */
interface Arithmetic
{
    /**
     * Increments a given value by the step value supplied.
     * Useful for shared counters and other persistent integer based
     * tracking.
     *
     * @param   string    id of cache entry to increment
     * @param   int       step value to increment by
     * @return  integer
     * @return  boolean
     */
    public function increment($id, $step = 1);
    /**
     * Decrements a given value by the step value supplied.
     * Useful for shared counters and other persistent integer based
     * tracking.
     *
     * @param   string    id of cache entry to decrement
     * @param   int       step value to decrement by
     * @return  integer
     * @return  boolean
     */
    public function decrement($id, $step = 1);
}

// End Kohana_Cache_Arithmetic