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
 * @copyright	(C) 2005-2015 Kilofox Studio
 */
class ArticleModel extends \Bootphp\ORM\ORM
{
    //private $_values = null;
    //private $_loaded = false;
    protected $_tableName = 'articles';
    protected $_table_name = 'articles';

    /**
     * 创建并返回一个新的模型对象。
     *
     * @return	对象
     */
    public static function factory($name, $application = 'system')
    {
        return parent::factory($name, $application);
    }

    /**
     * 根据主键加载数据，并返回对象
     * @return	对象
     */
    public function load($id = 0)
    {
        if (is_numeric($id) && $id > 0) {
            $this->_values = DB::table($this->tableName)->select('*')->where('id', $id)->first();
            $this->_loaded = true;
        }
        return $this->_values;
    }

    /**
     * 根据指定的分类取得文章
     */
    public function findByCategory($cateId = 0)
    {
        if ($cateId <= 0)
            return null;
        $values = DB::select()->from($this->tableName)->where('category', '=', $cateId)->execute()->asArray();
        return $values;
    }

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
