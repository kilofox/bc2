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
	protected $tableName = 'system_menu';
	/**
	 * 创建并返回一个新的模型对象。
	 *
	 * @return	对象
	 */
	public static function factory($name, $application = 'core')
	{
		return parent::factory($name, $application);
	}
	/**
	 *
	 */
	public function menu($current = '')
	{
		$menus = $this->findAll();
		$menu = ['tabs' => [], 'subMenu' => []];
		$defaultId = 0;
		$subMenu = [];
		foreach( $menus as $node )
		{
			if ( $node->parent_id == 0 )
			{
				$menu['tabs'][$node->id] = $node;
				$menu['tabs'][$node->id]->subMenu[$node->application] = $node;
				if ( $node->application == $current )
					$defaultId = $node->id;
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
				$menu['tabs'][$node->parent_id]->subMenu[$node->application] = $node;
				if ( $node->application == $current )
					$defaultId = $node->parent_id;
			}
		}
		if ( !empty($menu['tabs'][$defaultId]->subMenu) )
		{
			$menu['subMenu'] = $menu['tabs'][$defaultId]->subMenu;
		}
		return $menu;
	}
}