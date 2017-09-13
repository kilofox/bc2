<?php

namespace App\Controller\admin;

use Bootphp\Auth\Auth;
use Bootphp\ORM\ORM;

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

        $this->user = Auth::instance()->getUser();
        if (!$this->user) {
            $this->redirect('admin/login');
        }
    }

    /**
     * After 方法
     */
    public function after()
    {
        $menu = ORM::factory('menu')->menu($this->request->controller());

        $this->view->layout('layout')
            ->layoutPath(APP_PATH . '/views/admin/')
            ->templatePath(APP_PATH . '/views/admin/')
            ->set('user', $this->user)
            ->set('menu', $menu)
            ->set('title', $this->title);

        parent::after();
    }

    /**
     * 控制器菜单数组
     */
    final public function menu($default = '')
    {
        $menu = ORM::factory('menu')->menu($default);

        return $menu;
    }

    /**
     * 公共列表
     */
    public function commonList($list)
    {
        $allSelect = false;
        $this->view->template('commonList')
            ->set('keyList', $this->keyList)
            ->set('list', $list)
            ->set('allSelect', $allSelect);
    }

    /**
     * 公共编辑
     */
    public function commonEdit($node)
    {
        $this->view->template('commonEdit')
            ->set('keyList', $this->keyList)
            ->set('node', $node)
            ->set('updateUrl', $this->updateUrl);
    }

}
