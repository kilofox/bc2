<?php
/**
 * 单页控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */

namespace App\index\controllers;
use Bootphp\Model;
class IndexController extends \Bootphp\Controller
{
	/**
	 * Before 方法
	 */
	public function before()
	{
		$this->routes['<id>/comments'] = 'comments';
		$this->layoutPath = APP_PATH . '/index/views/default/';
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
	public function indexAction()
	{
		$articles = Model::factory('article', 'articles')->findAll();
		$this->assign('nodes', $articles);
	}
	public function commentsAction()
	{
		echo 'this->application = ', $this->application;
		echo '<br/>';
		echo 'this->controller = ', $this->controller;
		echo '<br/>';
		echo 'this->action = ', $this->action;
		//echo $this->id;
		echo '这是一些评论。';
	}
}