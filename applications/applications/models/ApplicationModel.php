<?php

namespace App\applications\models;
use Bootphp\Database\DB;
/**
 * 菜单模型。
 *
 * @package	BootCMS
 * @category	模型
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class ApplicationModel extends \Bootphp\Model
{
	private $_values = NULL;
	private $_loaded = false;
	protected $tableName = 'applications';
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
		$applications = $this->findAll();
		$menu = ['tabs' => [], 'subMenu' => []];
		$defaultId = 0;
		foreach( $applications as $app )
		{
			if ( $app->parent == 0 )
			{
				$menu['tabs'][$app->id] = $app;
				$menu['tabs'][$app->id]->subMenu[$app->application] = $app;
				if ( $app->application == $current )
					$defaultId = $app->id;
			}
			else
			{
				$menu['tabs'][$app->parent]->subMenu[$app->application] = $app;
				if ( $app->application == $current )
					$defaultId = $app->parent;
			}
		}
		if ( !empty($menu['tabs'][$defaultId]->subMenu) )
		{
			$menu['subMenu'] = $menu['tabs'][$defaultId]->subMenu;
		}
		return $menu;
	}
}