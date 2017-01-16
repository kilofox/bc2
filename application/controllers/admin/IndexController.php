<?php

namespace App\controllers\admin;

use App\controllers\admin\AdministrationController;
use App\models\articles\ArticleModel;
use Bootphp\Model;
use Bootphp\Database\DB;

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
        $articles = Model::factory('article', 'articles')->count();
        // 统计评论数
        $comments = Model::factory('comment', 'articles')->count();
        // 查询数据库版本
        $dbVersion = DB::select([DB::expr('version()'), 'version'])->execute()->get('version');
        $this->assign('articles', $articles);
        $this->assign('comments', $comments);
        $this->assign('dbVersion', $dbVersion);
    }

}
