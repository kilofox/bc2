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
class ArticleModel extends \Bootphp\ORM\ORM
{
    protected $_object_name = 'article';
    protected $tableName = 'articles';
    protected $hasMany = array(
        'categories' => array(
            'model' => 'category',
            'through' => 'articles_categories',
            'far_key' => 'category_id',
            //'foreign_key' => 'id'
        ),
    );

    /**
     * 文章列表
     */
    public function articleList($itemsPerPage = 10, $baseUrl = '')
    {
        $list = ['data' => null];
        $articles = $this->findAll();
        foreach ($articles as &$node) {
            $node->created = \Bootphp\Date::unixToHuman($node->created);
            switch ($node->status) {
                case '0':
                    $node->status = '垃圾筒';
                    break;
                case '1':
                    $node->status = '已发布';
                    break;
                case '2':
                    $node->status = '草稿';
                    break;
                case '3':
                    $node->status = '待审核';
                    break;
            }
            $node->operation = '<a href="' . $baseUrl . '/articles/admin/' . $node->id . '/edit">编辑</a>';
        }
        $pager = \Bootphp\Pagination\Pagination::factory(array(
                    'total_items' => count($articles),
                    'items_per_page' => $itemsPerPage,
                    'first_page_in_url' => true,
                    'view' => 'metro'
        ));
        $list['data'] = $articles;
        $list['pager'] = $pager->render();
        return $list;
    }

}
