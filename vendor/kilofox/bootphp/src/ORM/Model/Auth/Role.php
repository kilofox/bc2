<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Default auth role
 *
 * @package    Bootphp/Auth
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kilofox.net/license.html
 */
class Model_Auth_Role extends ORM
{
    // Relationships
    protected $_has_many = array(
        'users' => array('model' => 'User', 'through' => 'roles_users'),
    );

    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array('min_length', array(':value', 4)),
                array('max_length', array(':value', 32)),
            ),
            'description' => array(
                array('max_length', array(':value', 255)),
            )
        );
    }

}

// End Auth Role Model
