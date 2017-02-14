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
    protected $hass2Many = [
        //'users' => ['model' => 'User', 'through' => 'roles_users'],
    ];

    public function rules()
    {
        return [
            'name' => [
                ['not_empty'],
                ['min_length', [':value', 4]],
                ['max_length', [':value', 32]],
            ],
            'description' => [
                ['max_length', [':value', 255]],
            ]
        ];
    }

}
