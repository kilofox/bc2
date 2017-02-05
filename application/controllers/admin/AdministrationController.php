<?php

namespace App\controllers\admin;

use Bootphp\Auth\Auth;
use Bootphp\Model;

/**
 * 后台首页控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class AdministrationController extends \Bootphp\Controller
{
    public $title = '';
    protected $keyList = [];
    protected $updateUrl = '';

    /**
     * Before 方法
     */
    public function before()
    {
        parent::before();
        $this->layoutPath = APP_PATH . '/modules/system/views/default/';
        $this->templatePath = APP_PATH . '/modules/system/views/default/admin/';
        $this->user = Auth::instance()->get_user();
        if (!$this->user)
            $this->redirect('admin/login');
    }

    /**
     * After 方法
     */
    public function after()
    {
       // $this->assign('user', $this->user);
       // $this->assign('menu', $this->menu($this->module));
       // $this->assign('title', $this->title);

        parent::after();
    }

    /**
     * 控制器菜单数组
     */
    final public function menu($default = '')
    {
        $menu = Model::factory('menu', 'system')->menu($default);
        //print_r($menu);
        return $menu;
    }

    /**
     * 公共列表
     */
    public function commonList($list)
    {
        $this->assign('keyList', $this->keyList);
        $this->assign('list', $list);
        $this->template = 'commonList';
    }

    /**
     * 公共编辑
     */
    public function commonEdit($node)
    {
        $this->assign('keyList', $this->keyList);
        $this->assign('node', $node);
        $this->assign('updateUrl', $this->updateUrl);
        $this->template = 'commonEdit';
    }

}
