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
class PublicController extends \Bootphp\Controller
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
	/*
	 * 默认方法
	 * 该方法将节点加载到一个页面中。
	 */
	public function loginAction()
	{
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$caption = '登录失败';
			$content = '您输入的密码或用户名有误。';
			Auth::instance()->logout();
			// 尝试登录
			Auth::instance()->login(strtolower($this->request->post('username')), $this->request->post('password'), false);
			$this->user = Auth::instance()->get_user();
			if ( $this->user )
			{
				$status = 1;
				$caption = '登录成功';
				$content = '您已经成功登录。';
			}
			$this->ajaxReturn($status, array($caption, $content), NULL);
		}
		Auth::instance()->logout();
		$this->view->layout(false);
	}
	/**
	 * 用户退出
	 *
	 * @return	void
	 */
	public function logoutAction()
	{
		// 注销用户
		Auth::instance()->logout();
		// 重定向到登录页
		$this->redirect('admin/public/login');
	}
}