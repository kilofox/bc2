<?php

namespace App\Controller\admin;

use Bootphp\ORM\ORM;
use Bootphp\Database\DB;
use Bootphp\View;
use App\Controller\admin\AdministrationController;
use App\Model\UserModel;

/**
 * 后台首页控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
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
        $articles = ORM::factory('article')->count();

        // 统计评论数
        $comments = ORM::factory('comment')->count();

        // 查询数据库版本
        $dbVersion = DB::select([DB::expr('version()'), 'version'])->execute()->get('version');
        //$user->where('id', '=', 1)->find();
        //$user->article;
        //$articles = $user->article->findAll();
        //print_r($user);

        //$article = ORM::factory('Article')->where('id', '=', 1)->find();
       // $articless = $article->categories
        //    ->distinct(true)
        //    ->orWhereOpen()
        //    ->orWhere('articles_categories.id', '=', 2)
        //    ->orWhereClose()
         //   ->having('articles_categories.id', 'in', [1, 2, 3, 4, 5])
        //    ->groupBy('articles_categories.id')
         //   ->orderBy('articles_categories.id', 'desc')
         //   ->limit(5)
         //   ->findAll();
        //$article->has('categories', $category);
        //print_r($article);
        //$article->introduce = 'introduce_' . mt_rand();
        //$article->save();
        //$article->countAll();
        //print_r($article);
        //exit;
        // $user = DB::select()->from('users')->where('id', '=', 1)->as_object()->execute();
        // $aa = $user[0];


        $this->view->set('articles', $articles)
            ->set('comments', $comments)
            ->set('dbVersion', $dbVersion)
            ->template('index');
    }

}
