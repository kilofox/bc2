<?php

namespace App\admin\controllers;
use App\admin\controllers\AdministratorController;
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
class UsersController extends AdministratorController
{
	/**
	 * Before 方法
	 */
	public function before()
	{
		$this->routes['role/<id>/edit'] = 'roleEdit';
		$this->routes['role/edit'] = 'roleEdit';
		$this->routes['role/create'] = 'roleCreate';
		$this->routes['role/delete'] = 'roleDelete';
		$this->routes['<id>/edit'] = 'edit';
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
		// 用户列表
		$users = Model::factory('user', 'users')->findAll();
		foreach( $users as &$node )
		{
			$node->created = \Bootphp\Date::unixToHuman($node->created);
		}
		$this->tab = 'users';
		$this->assign('nodes', $users);
	}
	/**
	 * 编辑用户
	 */
	public function editAction()
	{
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';

			$userId = intval($this->request->post('nid'));
			$oUser = Model::factory('user', 'users');
			$user = $oUser->find($userId);
			if ( !$user )
			{
				$status = 3;
				$info = '请求的用户不存在。';
				$this->ajaxReturn($status, $info);
			}
			try
			{
				$update = [
					'nickname' => $this->request->post('nickname'),
					'email' => $this->request->post('email'),
					'address' => $this->request->post('address'),
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
		$aid = intval($this->request->param('id'));
		$user = Model::factory('user', 'users')->find($aid);
		$this->assign('node', $user);
	}
}