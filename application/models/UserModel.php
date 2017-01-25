<?php

namespace App\models;

/**
 * 用户模型。
 *
 * @package     BootCMS
 * @category    模型
 * @author      Tinsh
 * @copyright   (C) 2005-2017 Kilofox Studio
 */
class UserModel extends \Bootphp\ORM\ORM
{
    protected $_object_name = 'user';
    protected $_table_name = 'users';
    protected $_has_many = array(
        'article' => array(
            //'model' => 'article',
            'foreign_key' => 'author_id'
        ),
    );

}
