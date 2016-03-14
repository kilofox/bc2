<?php

namespace App\admin\controllers;
use Bootphp\Auth\Auth;
use Bootphp\Model;
use Bootphp\Date;
/**
 * 后台首页控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class AdministratorController extends \Bootphp\Controller
{
	public $tab = NULL;
	/**
	 * Before 方法
	 */
	public function before()
	{
		parent::before();
		$this->layoutPath = APP_PATH . '/admin/views/default/';
		$this->user = Auth::instance()->get_user();
		if ( !$this->user )
			$this->redirect('admin/public/login');
	}
	/**
	 * After 方法
	 */
	public function after()
	{
		$this->assign('user', $this->user);
		$this->assign('menus', $this->menus());
		$this->assign('tab', $this->tab ? $this->tab : $this->controller);
		parent::after();
	}
	/*
	 * 默认方法
	 * 该方法将节点加载到一个页面中。
	 */
	public function indexAction()
	{
		$countArticles = \Bootphp\Model::factory('article', 'articles')->count();
		$this->assign('countArticles', $countArticles);
	}
	/**
	 * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
	 */
	final public function menus()
	{
		//$menus = \Bootphp\Model::factory('menu', 'system')->findAll();
		//print_r($menus);
		$menus = [
			'admin' => [
				'name' => '系统',
				'controller' => 'index',
				'action' => 'index',
				'subMenus' => [
					'index' => [
						'name' => '概览',
						'action' => 'index'
					],
					'settings' => [
						'name' => '网站设置',
						'action' => 'index',
					],
					'menus' => [
						'name' => '菜单',
						'action' => 'index',
					]
				],
			],
			'users' => [
				'name' => '用户',
				'controller' => 'admin',
				'action' => 'index',
				'subMenus' => [
					'admin' => [
						'name' => '用户',
						'action' => 'index',
					],
					'roles' => [
						'name' => '角色',
						'action' => 'index',
					]
				],
			],
			'articles' => [
				'name' => '文章',
				'controller' => 'admin',
				'action' => 'index',
				'subMenus' => [
					'admin' => [
						'name' => '文章',
						'action' => 'index',
					],
					'categories' => [
						'name' => '分类',
						'action' => 'index',
					]
				],
			],
		];
		return $menus;
	}
}