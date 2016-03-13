<?php

namespace App\admin\models;
use Bootphp\Model;
use Bootphp\DB;
/**
 * 节点模型。
 *
 * @package		BootCMS
 * @category	模型
 * @author		Tinsh
 * @copyright	(C) 2005-2015 Kilofox Studio
 */
class SiteModel extends Model
{
	protected $tableName = 'sites';
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