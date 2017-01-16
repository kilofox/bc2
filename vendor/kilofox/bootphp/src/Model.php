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
     * @param   string  $name   model name
     * @return  Model
     */
    public static function factory($name)
    {
        // Add the model prefix
        $class = 'Model_' . $name;

        return new $class;
    }

}
