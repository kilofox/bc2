<?php

namespace App\system\controllers;
use Bootphp\Auth\Auth;
use Bootphp\Model;
/**
 * 后台首页控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class AdministrationController extends \Bootphp\Controller
{
	public $tab = NULL;
	protected $keyList = [];
	/**
	 * Before 方法
	 */
	public function before()
	{
		parent::before();
		$this->templatePath = APP_PATH . '/system/views/default/admin/';
		$this->layoutPath = APP_PATH . '/system/views/default/';
		$this->user = Auth::instance()->get_user();
		if ( !$this->user )
			$this->redirect('system/login');
	}
	/**
	 * After 方法
	 */
	public function after()
	{
		$this->assign('user', $this->user);
		$this->assign('menu', $this->menu($this->application));
		$this->assign('tab', $this->tab ? $this->tab : $this->controller);
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
	public function commonList($list = [])
	{
		$this->assign('keyList', $this->keyList);
		$this->assign('list', $list);
		$this->template = 'commonList';
	}
}