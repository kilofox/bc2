<?php

namespace Bootphp;
/**
 * Abstract controller class. Controllers should only be created using a [Request].
 *
 * Controllers methods will be automatically called in the following order by
 * the request:
 *
 * 	$controller = new FooController($request, $response);
 * 	$controller->before();
 * 	$controller->barAction();
 * 	$controller->after();
 *
 * The controller action should add the output it creates to
 * `$this->response->body($output)`, typically in the form of a [View], during the
 * "action" part of execution.
 *
 * @package	BootPHP
 * @category	Controller
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
abstract class Controller
{
	/**
	 * @var	Request Request that created the controller
	 */
	public $request;
	/**
	 * @var	Response The response that will be returned from controller
	 */
	public $response;
	/**
	 * @var string 应用
	 */
	public $application;
	/**
	 * @var string 控制器
	 */
	public $controller;
	/**
	 * @var string 动作
	 */
	public $action;
	/**
	 * @var	View 页面模板
	 */
	public $template;
	/**
	 * @var	View 模板文件路径
	 */
	public $templatePath;
	/**
	 * @var	View 布局模板
	 */
	public $layout = 'layout';
	/**
	 * @var	View 布局文件路径
	 */
	public $layoutPath;
	/**
	 * @var	View 页面模板
	 */
	public $view;
	/**
	 * @var	boolean	自动渲染模板
	 * */
	public $autoRender = true;
	/**
	 * @var array 路径
	 */
	public $paths = [];
	/**
	 * @var array 路由规则
	 */
	public $routes = [];
	/**
	 * Creates a new controller instance. Each controller must be constructed
	 * with the request object that created it.
	 *
	 * @param Request $request Request that created the controller
	 * @param Response $response The request's response
	 * @return	void
	 */
	public function __construct(Request $request, Response $response)
	{
		// Assign the request to the controller
		$this->request = $request;
		// Assign a response to the controller
		$this->response = $response;
	}
	/**
	 * Automatically executed before the controller action. Can be used to set
	 * class properties, do authorization checks, and execute other custom code.
	 *
	 * @return	void
	 */
	protected function before()
	{
		// Load View
		$this->view = new \Bootphp\View();

		// Default template
		$this->template = $this->action;
		$this->templatePath = APP_PATH . '/' . $this->application . '/views/default/' . $this->controller . '/';

		// Default layout path
		$this->layoutPath = APP_PATH . '/index/views/default/';
	}
	/**
	 * Automatically executed after the controller action. Can be used to apply
	 * transformation to the response, add extra output, and execute
	 * other custom code.
	 *
	 * @return	void
	 */
	protected function after()
	{
		if ( $this->autoRender )
		{
			$this->view->path($this->templatePath);
			$this->view->file($this->template);
			$this->view->layout($this->layout);
			$this->view->layoutPath($this->layoutPath);
			$this->view->set([
				'application' => $this->application,
				'controller' => $this->controller,
				'action' => $this->action,
				'baseUrl' => $this->baseUrl
			]);
			echo $this->view->render();
		}
	}
	/**
	 * 为模板分配变量
	 *
	 * @param $name 模板变量
	 * @param $value 模板变量的值
	 * @return	void
	 */
	protected function assign($name, $value = '')
	{
		if ( $this->autoRender )
		{
			$this->view->set([$name => $value]);
		}
	}
	/**
	 * 重定向。
	 *
	 * @param	string	$location 生成URL的地址或URL
	 * @param type $outter 是站外链接吗？
	 * @return	void
	 */
	public function redirect($location, $outter = false)
	{
		$outter === false and $location = $this->baseUrl . '/' . $location;
		$this->response->redirect($location)->send();
		exit;
	}
	/**
	 * Ajax方式返回数据到客户端
	 * @access protected
	 * @param mixed $data 要返回的数据
	 * @param String $info 提示信息
	 * @param boolean $status 返回状态
	 * @param String $status ajax返回类型 JSON XML
	 * @return	void
	 */
	public function ajaxReturn($status = 0, $info = '', $data = NULL)
	{
		$result = new \stdClass();
		$result->status = $status;
		$result->info = $info;
		$result->data = $data;
		exit(json_encode($result));
	}
}