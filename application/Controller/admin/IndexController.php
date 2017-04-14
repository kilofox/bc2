<?php

namespace App\Controller\admin;

use Bootphp\ORM\ORM;
use Bootphp\Database\DB;
use App\Controller\admin\AdministrationController;

/**
 * 后台首页控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2017 Kilofox Studio
 */
class IndexController extends AdministrationController
{
    /**
     * Before 方法
     */
    public function before()
    {
        parent::before();
    }

    /**
     * After 方法
     */
    public function after()
    {
        parent::after();
    }

    /*
     * 默认方法
     */
    public function indexAction()
    {
        // 统计文章数
        $articles = ORM::factory('article')->cached()->count();

        // 统计评论数
        $comments = ORM::factory('comment')->cached()->count();

        // 查询数据库版本
        $dbVersion = DB::select([DB::expr('VERSION()'), 'version'])->execute()->get('version');

        $article = ORM::factory('Article')->from(['articles', 'a'])->where('a.id', '=', 2)->groupBy('a.id')->cached(0)->find();
       // print_r($article);
        $cats = $article->author;
       // print_r($cats);
//            ->distinct(true)
//            ->orWhereOpen()
//            ->orWhere('articles_categories.id', '=', 2)
//            ->orWhereClose()
//            ->having('articles_categories.id', 'in', [1, 2, 3, 4, 5])
//            ->groupBy('articles_categories.id')
//            ->orderBy('articles_categories.id', 'desc')
//            ->limit(5)
//            ->findAll();
        //$article->has('categories', $category);
        //print_r($articles);

        $this->view->set('articles', $articles)
            ->set('comments', $comments)
            ->set('dbVersion', $dbVersion)
            ->template('index/index');
    }

}
