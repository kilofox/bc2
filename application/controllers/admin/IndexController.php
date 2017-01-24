<?php

namespace App\controllers\admin;

use App\controllers\admin\AdministrationController;
use App\models\articles\ArticleModel;
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
        //$articles = \Bootphp\ORM\ORM::factory('article')->find_all();
        $linkages = \Bootphp\ORM\ORM::factory('Linkage')->where('id', '=', 16)->find_all();
        //$linkages = DB::select()->from('linkages')->where('id', '=', 16)->as_object()->execute();
print_r(\Bootphp\ORM\ORM::factory('Linkage'));
        // 统计评论数
        $comments = 0; //Model::factory('comment')->count();
        // 查询数据库版本
        $dbVersion = 2; //DB::select([DB::expr('version()'), 'version'])->execute()->get('version');
        $view = View::factory(APP_PATH . '/views/default/admin/test.php');
        // $this->assign('articles', $articles);
        // $this->assign('comments', $comments);
        // $this->assign('dbVersion', $dbVersion);
        $view->set('linkages', $linkages);
        $this->response->body($view);
    }

}
