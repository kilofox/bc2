<?php

namespace Bootphp\Cache\Cache;

/**
 * Kohana Cache Tagging Interface
 *
 * @package    Bootphp/Cache
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kohanaphp.com/license
 */
interface Tagging
{
    /**
     * Set a value based on an id. Optionally add tags.
     *
     * Note : Some caching engines do not support
     * tagging
     *
     * @param   string   $id        id
     * @param   mixed    $data      data
     * @param   integer  $lifetime  lifetime [Optional]
     * @param   array    $tags      tags [Optional]
     * @return  boolean
     */
    public function set_with_tags($id, $data, $lifetime = null, array $tags = null);
    /**
     * Delete cache entries based on a tag
     *
     * @param   string  $tag  tag
     */
    public function delete_tag($tag);
    /**
     * Find cache entries based on a tag
     *
     * @param   string  $tag  tag
     * @return  array
     */
    public function find($tag);
}
