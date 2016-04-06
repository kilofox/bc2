<?php
/**
 * 单页控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */

namespace App\core\controllers;
class PagesController extends \Bootphp\Controller
{
	/**
	 * Before 方法
	 */
	public function before()
	{
		$this->routes['<id>/comments'] = 'comments';
		parent::before();
		$this->model = \Bootphp\Model::factory('node');
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
		$node = $this->model->load(1);
		print_r($node);
		$nodes = $this->model->findAll();
		$items = [];
		$this->assign('products', $nodes);
		$this->template = 'pages';
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