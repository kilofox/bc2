<?php

namespace App\Model;

use Bootphp\Model;
use Bootphp\Database\DB;

/**
 * Role model.
 *
 * @package     BootCMS
 * @category    Model
 * @author      Tinsh
 * @copyright   (C) 2005-2017 Kilofox Studio
 * @license     http://kilofox.net/license.html
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
