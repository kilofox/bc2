<?php

namespace Bootphp\Cache\Cache;

/**
 * Garbage Collection interface for caches that have no GC methods
 * of their own, such as [Cache_File] and [Cache_Sqlite]. Memory based
 * cache systems clean their own caches periodically.
 *
 * @package    Bootphp/Cache
 * @category   Base
 * @version    2.0
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
interface CacheGarbageCollect
{
    /**
     * Garbage collection method that cleans any expired
     * cache entries from the cache.
     *
     * @return void
     */
    public function garbage_collect();
}
