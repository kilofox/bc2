<?php

namespace Bootphp\Auth\Model\ORM;

/**
 * Default auth role.
 *
 * @package    Bootphp/Auth
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kilofox.net/license.html
 */
class RoleModel extends \Bootphp\ORM\ORM
{
    /**
     * Table name.
     *
     * @var string  Table name
     */
    protected $tableName = 'roles';

    /**
     * Relationships.
     *
     * @var array
     */
    protected $hasMany = array(
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
