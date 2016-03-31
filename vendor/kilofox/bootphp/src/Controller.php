<?php

namespace Bootphp;
/**
 * 抽象控制器类。控制器应该只能使用 [Request] 来创建。
 * 控制器方法会按下列顺序由请求自动调用：
 *
 * 	$controller = new FooController($request, $response);
 * 	$controller->before();
 * 	$controller->barAction();
 * 	$controller->after();
 *
 * 控制器动作通常在动作执行期间以 [View] 的形式将创建的输出添加到 `$this->response->body($output)`。
 *
 * @package BootPHP
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
abstract class Controller
{
	/**
	 * @var	Request 创建控制器的请求
	 */
	public $request;
	/**
	 * @var	Response 从控制器返回的响应
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
	 * 创建一个新的控制器实例。每个控制器必须由请求对象与响应对象来构造。
	 *
	 * @param Request $request 创建控制器的请求
	 * @param Response $response 请求的响应
	 * @return	void
	 */
	public function __construct(Request $request, Response $response)
	{
		// 将请求分配给控制器
		$this->request = $request;
		// 将响应分配给控制器
		$this->response = $response;
	}
	/**
	 * 在控制器动作之前自动执行。可以用来设置类的属性，执行其它自定义代码。
	 *
	 * @return	void
	 */
	protected function before()
	{
		$route = new \Bootphp\Route();
		$route->set($this->routes);
		unset($this->paths[0], $this->paths[1]);
		list($action, $params) = $route->matches(array_values($this->paths));
		$this->action = $action;
		foreach( $params as $name => $value )
			$this->request->{$name} = $value;
		// 加载视图
		$this->view = new \Bootphp\View();
		// 默认模板
		$this->template = $action;
		$this->templatePath = APP_PATH . '/' . $this->application . '/views/default/' . $this->controller . '/';
		// 默认布局
		$this->layoutPath = APP_PATH . '/index/views/default/';
	}
	/**
	 * 在控制器动作之后自动执行。可以用来对请求的响应实施转换，添加额外输出，执行其它自定义代码。
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