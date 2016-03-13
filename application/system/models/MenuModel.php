<?php

namespace App\system\models;
use Bootphp\Model;
use Bootphp\Database\DB;
/**
 * 菜单模型。
 *
 * @package BootCMS
 * @category 模型
 * @author Tinsh
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class MenuModel extends Model
{
	private $_values = NULL;
	private $_loaded = false;
	protected $tableName = 'region_blocks';
	/**
	 * 创建并返回一个新的模型对象。
	 *
	 * @return	对象
	 */
	public static function factory($name, $application = 'core')
	{
		return parent::factory($name, $application);
	}
}