<?php

namespace Bootphp;

/**
 * Model base class. All models should extend this class.
 *
 * @package    Bootphp
 * @category   Models
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Model
{
    /**
     * Create a new model instance.
     *
     *     $model = Model::factory($name);
     *
     * @param   string  $name   Model name
     * @param   string  $module Module name
     * @return  Model
     */
    public static function factory($name, $module = null)
    {
        $module = !is_string($module) ? '' : $module .= '\\';

        // Add the model suffix
        $class = 'App\\models\\' . $module . ucfirst($name) . 'Model';

        return new $class;
    }

}
