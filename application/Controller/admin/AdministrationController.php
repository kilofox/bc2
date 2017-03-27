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
        $this->view->set('user', $this->user)
            ->set('menu', $this->menu($this->request->controller()))
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
        $this->view ->templatePath(APP_PATH . '/View/admin/')
            ->template('commonList')
            ->set('keyList', $this->keyList)
            ->set('list', $list);
    }

    /**
     * 公共编辑
     */
    public function commonEdit($node)
    {
        $this->view->set('keyList', $this->keyList)
            ->set('node', $node)
            ->set('updateUrl', $this->updateUrl)
            ->template('commonEdit');
    }

}
