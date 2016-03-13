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
		$this->assign('menus', $this->getMenus());
		$this->assign('tab', $this->tab ? $this->tab : $this->action);
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
	final public function getMenus($controller = '')
	{
		$menus = array(
			'admin' => array(
				'name' => '系统',
				'controller' => 'index',
				'action' => 'index',
				'child' => array(
					'index' => array('name' => '概览'),
					'settings' => array('name' => '网站设置'),
				)
			),
			'users' => array(
				'name' => '用户',
				'controller' => 'admin',
				'action' => 'index',
				'child' => array(
					'users' => array('name' => '用户'),
					'roles' => array('name' => '角色'),
				)
			),
			'articles' => array(
				'name' => '文章',
				'controller' => 'admin',
				'action' => 'index',
				'child' => array(
					'articles' => array('name' => '文章'),
					'categories' => array('name' => '分类'),
				)
			),
		);
		return $menus;
		// 获取主菜单
		$where['pid'] = 0;
		$where['hide'] = 0;
		$menus['main'] = M('Menu')->where($where)->order('sort asc')->select();
		$menus['child'] = array(); //设置子节点
		//高亮主菜单
		$current = M('Menu')->where("url like '%{$controller}/" . ACTION_NAME . "%'")->field('id')->find();
		if ( $current )
		{
			$nav = D('Menu')->getPath($current['id']);
			$nav_first_title = $nav[0]['title'];
			foreach( $menus['main'] as $key => $item )
			{
				if ( !is_array($item) || empty($item['title']) || empty($item['url']) )
				{
					$this->error('控制器基类$menus属性元素配置有误');
				}
				if ( stripos($item['url'], MODULE_NAME) !== 0 )
				{
					$item['url'] = MODULE_NAME . '/' . $item['url'];
				}
				// 判断主菜单权限
				if ( !IS_ROOT && !$this->checkRule($item['url'], AuthRuleModel::RULE_MAIN, null) )
				{
					unset($menus['main'][$key]);
					continue; //继续循环
				}

				// 获取当前主菜单的子菜单项
				if ( $item['title'] == $nav_first_title )
				{
					$menus['main'][$key]['class'] = 'current';
					//生成child树
					$groups = M('Menu')->where("pid = {$item['id']}")->distinct(true)->field("`group`")->select();
					if ( $groups )
					{
						$groups = array_column($groups, 'group');
					}
					else
					{
						$groups = array();
					}

					//获取二级分类的合法url
					$where = array();
					$where['pid'] = $item['id'];
					$where['hide'] = 0;
					$second_urls = M('Menu')->where($where)->getField('id,url');

					if ( !IS_ROOT )
					{
						// 检测菜单权限
						$to_check_urls = array();
						foreach( $second_urls as $key => $to_check_url )
						{
							if ( stripos($to_check_url, MODULE_NAME) !== 0 )
							{
								$rule = MODULE_NAME . '/' . $to_check_url;
							}
							else
							{
								$rule = $to_check_url;
							}
							if ( $this->checkRule($rule, AuthRuleModel::RULE_URL, null) )
								$to_check_urls[] = $to_check_url;
						}
					}
					// 按照分组生成子菜单树
					foreach( $groups as $g )
					{
						$map = array('group' => $g);
						if ( isset($to_check_urls) )
						{
							if ( empty($to_check_urls) )
							{
								// 没有任何权限
								continue;
							}
							else
							{
								$map['url'] = array('in', $to_check_urls);
							}
						}
						$map['pid'] = $item['id'];
						$map['hide'] = 0;
						$menuList = M('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
						$menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
					}
					if ( $menus['child'] === array() )
					{
						//$this->error('主菜单下缺少子菜单，请去系统=》后台菜单管理里添加');
					}
				}
			}
		}
		return $menus;
	}
}