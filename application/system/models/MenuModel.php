<?php

namespace App\system\models;
use Bootphp\Database\DB;
/**
 * 菜单模型。
 *
 * @package BootCMS
 * @category 模型
 * @author Tinsh
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class MenuModel extends \Bootphp\Model
{
	private $_values = NULL;
	private $_loaded = false;
	protected $tableName = 'system_menus';
	/**
	 * 创建并返回一个新的模型对象。
	 *
	 * @return	对象
	 */
	public static function factory($name, $application = 'system')
	{
		return parent::factory($name, $application);
	}
	/**
	 * 系统菜单
	 */
	public function menu($current = '')
	{
		$menus = $this->order('sort')->findAll();
		$menu = ['tabs' => [], 'default' => 0];
		$defaultId = 0;
		$subMenu = [];
		foreach( $menus as $node )
		{
			if ( $node->parent_id == 0 )
			{
				$menu['tabs'][$node->id] = $node;
				$menu['tabs'][$node->id]->apps[] = $node->application;
				$menu['tabs'][$node->id]->subMenu = [];
				if ( $node->application == $current )
					$menu['default'] = $node->id;
			}
			else
			{
				$subMenu[] = $node;
			}
		}
		foreach( $subMenu as $node )
		{
			if ( isset($menu['tabs'][$node->parent_id]) )
			{
				$menu['tabs'][$node->parent_id]->apps[] = $node->application;
				$menu['tabs'][$node->parent_id]->subMenu[] = $node;
			}
		}
		return $menu;
	}
}