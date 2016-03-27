<?php

namespace App\admin\controllers;
use Bootphp\Auth\Auth;
/**
 * 后台首页控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class LogoutController extends \Bootphp\Controller
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
	/**
	 * 用户退出
	 *
	 * @return	void
	 */
	public function indexAction()
	{
		// 注销用户
		Auth::instance()->logout();
		// 重定向到登录页
		$this->redirect('admin/login');
	}
}