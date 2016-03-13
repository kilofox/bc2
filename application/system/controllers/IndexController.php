<?php

namespace App\system\controllers;
use App\system\controllers\AdministratorController;
use App\articles\models\ArticleModel;
use Bootphp\Model;
use Bootphp\Database\DB;
/**
 * 后台首页控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class IndexController extends AdministratorController
{
	/**
	 * Before 方法
	 */
	public function before()
	{
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
		// 统计文章数
		$articles = Model::factory('article', 'articles')->count();
		// 统计评论数
		$comments = Model::factory('comment', 'articles')->count();
		// 查询数据库版本
		$dbVersion = DB::select([DB::expr('version()'), 'version'])->execute()->get('version');
		$this->assign('articles', $articles);
		$this->assign('comments', $comments);
		$this->assign('dbVersion', $dbVersion);
	}
}