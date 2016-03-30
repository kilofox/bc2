<?php

namespace App\system\controllers;
use App\system\controllers\AdminController;
use Bootphp\Model;
/**
 * 后台菜单控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class MenusController extends AdminController
{
	/**
	 * Before 方法
	 */
	public function before()
	{
		$this->routes['<id>/edit'] = 'edit';
		$this->routes['<id>/submenus'] = 'submenus';
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
	 */
	public function indexAction()
	{
		if ( $this->request->method() == 'POST' )
		{
			$this->creationAction();
		}
		$menus = Model::factory('menu', 'system')->order('sort')->findAll(['parent_id', 0]);
		$this->assign('nodes', $menus);
		$this->template = 'menus';
	}
	/*
	 * 默认方法
	 */
	public function submenusAction()
	{
		$parentId = $this->request->param('id');
		$menus = Model::factory('menu', 'system')->order('sort')->findAll(['parent_id', $parentId]);
		$this->assign('nodes', $menus);
		$this->template = 'menus';
	}
	/**
	 * 创建新菜单
	 */
	public function creationAction()
	{
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			$oItem = Model::factory('menu', 'system');
			try
			{
				$creation = [
					'title' => $this->request->post('title'),
					'parent_id' => $this->request->post('parent_id'),
					'sort' => $this->request->post('sort'),
					'application' => $this->request->post('application'),
					'controller' => $this->request->post('controller'),
					'action' => $this->request->post('action'),
					'icon' => $this->request->post('icon')
				];
				if ( $oItem->create($creation) )
				{
					$status = 1;
					$info = '菜单已经创建成功。';
				}
				else
				{
					$status = 5;
					$info = '菜单创建失败。';
				}
			}
			catch( Validation_Exception $e )
			{
				$errors = $e->errors('models');
				foreach( $errors as $ev )
				{
					$status = 4;
					$info = $ev;
					break;
				}
			}
			$this->ajaxReturn($status, $info);
		}
	}
	/**
	 * 编辑菜单
	 */
	public function editAction()
	{
		$itemId = intval($this->request->param('id'));
		$oItem = Model::factory('menu', 'system');
		$item = $oItem->find($itemId);
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			if ( !$item )
			{
				$status = 3;
				$info = '请求的菜单不存在。';
				$this->ajaxReturn($status, $info);
			}
			try
			{
				$update = [
					'title' => $this->request->param('title'),
					'parent_id' => $this->request->param('parent_id'),
					'sort' => $this->request->param('sort'),
					'application' => $this->request->param('application'),
					'controller' => $this->request->param('controller'),
					'action' => $this->request->param('action'),
					'icon' => $this->request->param('icon')
				];
				if ( $oItem->update($update, ['id', '=', $item->id]) )
				{
					$status = 1;
					$info = '菜单已经更新成功。';
				}
				else
				{
					$status = 5;
					$info = '菜单没有更新。';
				}
			}
			catch( Validation_Exception $e )
			{
				$errors = $e->errors('models');
				foreach( $errors as $ev )
				{
					$status = 4;
					$info = $ev;
					break;
				}
			}
			$this->ajaxReturn($status, $info);
		}
		$this->tab = 'menus';
		$this->assign('node', $item);
	}
}