<?php

namespace App\articles\controllers;
use App\system\controllers\AdministrationController;
use App\articles\models\ArticleModel;
use Bootphp\Model;
use Bootphp\Database\DB;
/**
 * 后台分类控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright (C) 2005-2016 Kilofox Studio
 */
class CategoriesController extends AdministrationController
{
	/**
	 * Before 方法
	 */
	public function before()
	{
		$this->routes['category/<id>/edit'] = 'categoryEdit';
		$this->routes['category/edit'] = 'categoryEdit';
		$this->routes['category/create'] = 'categoryCreate';
		$this->routes['category/<id>/delete'] = 'categoryDelete';
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
	/**
	 * 文章列表
	 */
	public function indexAction()
	{
		$this->categoriesAction();
	}
	/*
	 * 分类列表
	 */
	public function categoriesAction()
	{
		// 角色列表
		$categories = Model::factory('category', 'articles')->findAll();
		$this->assign('nodes', $categories);
		$this->templatePath = APP_PATH . '/' . $this->application . '/views/default/admin/';
		$this->template = 'categories';
	}
	/**
	 * 创建一个新的分类
	 */
	public function categoryCreateAction()
	{
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			$oCategory = Model::factory('category', 'articles');
			try
			{
				$create = [
					'name' => $this->request->post('name', ''),
					'slug' => $this->request->post('slug', ''),
					'description' => $this->request->post('description', ''),
				];
				if ( $oCategory->create($create) )
				{
					$status = 1;
					$info = '新的分类创建成功。';
				}
				else
				{
					$status = 5;
					$info = '新的分类创建失败。';
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
		$this->tab = 'categories';
		$cid = intval($this->request->param('id'));
		$category = Model::factory('category', 'articles')->find($cid);
		$this->assign('node', $category);
	}
	/**
	 * 编辑分类
	 */
	public function categoryEditAction()
	{
		$categoryId = intval($this->request->param('id'));
		$oCategory = Model::factory('category', 'articles');
		$category = $oCategory->find($categoryId);
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			if ( !$category )
			{
				$status = 3;
				$info = '请求的分类不存在。';
				$this->ajaxReturn($status, $info);
			}
			try
			{
				$update = [
					'name' => $this->request->param('name', ''),
					'slug' => $this->request->param('slug', ''),
					'description' => $this->request->param('description', ''),
				];
				if ( $oCategory->update($update, ['id', '=', $category->id]) )
				{
					$status = 1;
					$info = '分类已经更新成功。';
				}
				else
				{
					$status = 5;
					$info = '分类没有更新。';
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
		if ( !$category )
		{
			$info = '请求的分类不存在。';
			//$this->error($info);
		}
		$this->tab = 'categories';
		$this->assign('node', $category);
	}
	/**
	 * 删除分类
	 */
	public function categoryDeleteAction()
	{
		if ( $this->request->isAjax() )
		{
			$status = 0;
			$info = '您没有足够的权限进行此项操作。';
			$categoryId = intval($this->request->param('id'));
			$oCategory = Model::factory('category', 'articles');
			$category = $oCategory->find($categoryId);
			if ( !$category )
			{
				$status = 3;
				$info = '请求的分类不存在。';
				$this->ajaxReturn($status, $info);
			}
			$articles = Model::factory('article', 'articles')->findByCategory($category->id);
			if ( count($articles) > 0 )
			{
				$status = 3;
				$info = '指定的分类下存在文章，不可删除。';
				$this->ajaxReturn($status, $info);
			}
			try
			{
				if ( $oCategory->delete(['id', '=', $category->id]) )
				{
					$status = 1;
					$info = '分类已经删除。';
				}
				else
				{
					$status = 5;
					$info = '分类删除失败。';
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