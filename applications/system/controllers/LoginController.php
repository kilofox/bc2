<?php

namespace App\system\controllers;
use Bootphp\Auth\Auth;
/**
 * 后台首页控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class LoginController extends \Bootphp\Controller
{
	/**
	 * Before 方法
	 */
	public function before()
	{
		parent::before();
		$this->templatePath = APP_PATH . '/system/views/default/login/';
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
	public function indexAction()
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
		$this->layout = false;
		$this->template = 'login';
	}
}