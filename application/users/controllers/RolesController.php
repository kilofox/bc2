<?php

namespace App\users\controllers;
use App\system\controllers\AdministrationController;
use App\users\models\UserModel;
use Bootphp\Model;
use Bootphp\Database\DB;
/**
 * 后台角色控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2016 Kilofox Studio
 */
class RolesController extends AdministrationController
{
	/**
	 * Before 方法
	 */
	public function before()
	{
		$this->routes['<id>/edit'] = 'edit';
		$this->routes['<id>/delete'] = 'delete';
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
	 * 用户列表
	 */
	public function indexAction()
	{
		$this->rolesAction();
	}
	/*
	 * 角色列表
	 */
	public function rolesAction()
	{
		// 角色列表
		$this->keyList = [
			'id' => ['alias' => 'ID'],
			'name' => ['alias' => '昵称'],
			'description' => ['alias' => '描述'],
			'operation' => ['alias' => '操作'],
		];
		// 角色列表
		$list = Model::factory('role', 'users')->roleList(3, $this->baseUrl);
		$this->commonList($list);
	}
	/**
	 * 创建一个新的角色
	 */
	public function createAction()
	{
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			$oRole = Model::factory('role', 'users');
			try
			{
				$create = [
					'name' => $this->request->post('name', ''),
					'description' => $this->request->post('description', ''),
				];
				if ( $oRole->create($create) )
				{
					$status = 1;
					$info = '新用户创建成功。';
				}
				else
				{
					$status = 5;
					$info = '新用户创建失败。';
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
		$this->tab = 'roles';
	}
	/**
	 * 编辑角色
	 */
	public function editAction()
	{
		$roleId = intval($this->request->param('id'));
		$oRole = Model::factory('role', 'users');
		$role = $oRole->find($roleId);
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			if ( !$role )
			{
				$status = 3;
				$info = '请求的角色不存在。';
				$this->ajaxReturn($status, $info);
			}
			try
			{
				$update = [
					'name' => $this->request->param('name', ''),
					'description' => $this->request->param('description', ''),
				];
				if ( $oRole->update($update, ['id', '=', $role->id]) )
				{
					$status = 1;
					$info = '角色已经更新成功。';
				}
				else
				{
					$status = 5;
					$info = '角色没有更新。';
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
		$this->tab = 'roles';
		$this->assign('node', $role);
	}
	/**
	 * 删除角色
	 */
	public function deleteAction()
	{
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			$roleId = intval($this->request->param('id'));
			$oRole = Model::factory('role', 'users');
			$role = $oRole->find($roleId);
			if ( !$role )
			{
				$status = 3;
				$info = '请求的角色不存在。';
				$this->ajaxReturn($status, $info);
			}
			if ( $role->id <= 5 )
			{
				$status = 3;
				$info = '系统内置角色不可删除。';
				$this->ajaxReturn($status, $info);
			}
			try
			{
				if ( $oRole->delete(['id', '=', $role->id]) )
				{
					$status = 1;
					$info = '角色已经删除。';
				}
				else
				{
					$status = 5;
					$info = '角色删除失败。';
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
}