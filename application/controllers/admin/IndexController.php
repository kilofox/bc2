<?php

namespace App\controllers\admin;

use App\controllers\admin\AdministrationController;
use App\models\UserModel;
use Bootphp\Model;
use Bootphp\Database\DB;
use Bootphp\View;

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
        $user = new UserModel(1);
        //$user->where('id', '=', 1)->find();
        print_r($user->articles);
        $user->articles->find_all();

        //$user = DB::select()->from('users')->where('id', '=', 1)->as_object()->execute();
        //$user = $user[0];

        // 统计评论数
        $comments = 0; //Model::factory('comment')->count();
        // 查询数据库版本
        $dbVersion = 2; //DB::select([DB::expr('version()'), 'version'])->execute()->get('version');
        $view = View::factory(APP_PATH . '/views/default/admin/test.php');
        // $this->assign('articles', $articles);
        // $this->assign('comments', $comments);
        // $this->assign('dbVersion', $dbVersion);
        $view->set('user', $user);
        $this->response->body($view);
    }

}
