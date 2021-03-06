<?php

namespace App\Model;

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
    protected $tableName = 'article_categories';
    protected $hasManyd = array(
        'articles' => array(
            'model' => 'article',
        // 'through' => 'articles_categories',
        ),
    );
    protected $belongsTod = array(
        'articles' => array(
            'model' => 'article',
        // 'through' => 'articles_categories',
        ),
    );

}
