<?php

namespace App\models;

use Bootphp\Model;
use Bootphp\Database\DB;

/**
 * 节点模型。
 *
 * @package	BootCMS
 * @category	模型
 * @author		Tinsh
  @copyright  (C) 2005-2017 Kilofox Studio
 */
class CategoryModel extends \Bootphp\ORM\ORM
{
    protected $_tableName = 'article_categories';
    protected $_table_name = 'article_categories';
    protected $_has_many = array(
        'articles' => array(
            //'model' => 'article',
            'through' => 'article_categories',
        ),
    );

}
