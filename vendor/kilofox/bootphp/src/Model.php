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
     * @param   string  $name       Model name
     * @param   string  $directory  Model directory
     * @return  Model
     */
    public static function factory($name, $directory = null)
    {
        // Models are in a sub-directory
        $directory = !is_string($directory) ? '' : $directory .= '\\';

        // Add the model suffix
        $class = 'App\\models\\' . $directory . ucfirst($name) . 'Model';

        return new $class;
    }

}
