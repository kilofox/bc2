<?php

namespace Bootphp\Auth\Driver\ORM\Model;

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
    protected $hasMany = [
        'users' => ['model' => 'User', 'through' => 'roles_users'],
    ];

    public function rules()
    {
        return [
            'name' => [
                ['notEmpty'],
                ['minLength', [':value', 4]],
                ['maxLength', [':value', 32]],
            ],
            'description' => [
                ['maxLength', [':value', 255]],
            ]
        ];
    }

}
