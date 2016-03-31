<?php

namespace App\users\controllers;
use App\system\controllers\AdministrationController;
use App\users\models\UserModel;
use Bootphp\Model;
use Bootphp\Database\DB;
/**
 * 后台用户控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class AdminController extends AdministrationController
{
	/**
	 * Before 方法
	 */
	public function before()
	{
		$this->routes['<id>/edit'] = 'edit';
		parent::before();
		$this->layoutPath = APP_PATH . '/system/views/default/';
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
		$this->usersAction();
	}
	/*
	 * 用户列表
	 */
	public function usersAction()
	{
		$this->keyList = [
			'id' => ['alias' => 'ID'],
			'nickname' => ['alias' => '昵称', 'align' => 'center'],
			'email' => ['alias' => 'E-mail'],
			'created' => ['alias' => '注册时间'],
			'operation' => ['alias' => '操作'],
		];
		// 用户列表
		$list = Model::factory('user', 'users')->userList(3, $this->baseUrl);
		$this->commonList($list);
	}
	/**
	 * 编辑用户
	 */
	public function editAction()
	{
		$this->templatePath = APP_PATH . '/user/views/default/admin/';
		$userId = intval($this->request->param('id'));
		$oUser = Model::factory('user', 'users');
		$user = $oUser->find($userId);
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			if ( !$user )
			{
				$status = 3;
				$info = '请求的用户不存在。';
				$this->ajaxReturn($status, $info);
			}
			try
			{
				$update = [
					'nickname' => $this->request->param('nickname'),
					'email' => $this->request->param('email'),
					'address' => $this->request->param('address'),
				];
				if ( $oUser->update($update, ['id', '=', $user->id]) )
				{
					$status = 1;
					$info = '用户已经更新成功。';
				}
				else
				{
					$status = 5;
					$info = '用户没有更新。';
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
		$this->tab = 'index';
		$this->assign('node', $user);
	}
	/*
	 * 角色列表
	 */
	public function rolesAction()
	{
		// 角色列表
		$roles = Model::factory('role', 'users')->findAll();
		$this->assign('nodes', $roles);
	}
	/**
	 * 创建一个新的角色
	 */
	public function roleCreateAction()
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
	public function roleEditAction()
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
	public function roleDeleteAction()
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