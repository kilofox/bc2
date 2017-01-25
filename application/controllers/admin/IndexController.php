<?php

namespace App\controllers\admin;

use Bootphp\ORM\ORM;
use Bootphp\Database\DB;
use Bootphp\View;
use App\controllers\admin\AdministrationController;
use App\models\UserModel;


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
        $this->templatePath = APP_PATH . '/modules/system/views/default/index/';
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
        //$user = ORM::factory('User');
        //$user->where('id', '=', 1)->find();
        //$user->article;
        //$articles = $user->article->find_all();
        //print_r($user);

        $article = ORM::factory('Article');
        $articles = $article->where('id', '>', 1)->find_all();
        $categories = $article->categories->find_all();
        exit;


        // $user = DB::select()->from('users')->where('id', '=', 1)->as_object()->execute();
        // $aa = $user[0];
        // 统计评论数
        $comments = 0; //Model::factory('comment')->count();
        // 查询数据库版本
        $dbVersion = 2; //DB::select([DB::expr('version()'), 'version'])->execute()->get('version');
        $view = View::factory(APP_PATH . '/views/default/admin/test.php');
        // $this->assign('articles', $articles);
        // $this->assign('comments', $comments);
        // $this->assign('dbVersion', $dbVersion);
        isset($user) and $view->set('user', $user);
        isset($article) and $view->set('user', $article);
        $this->response->body($view);
    }

}
