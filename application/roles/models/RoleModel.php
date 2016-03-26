<?php

namespace App\users\models;
use Bootphp\Model;
use Bootphp\Database\DB;
/**
 * 用户模型。
 *
 * @package	BootCMS
 * @category	模型
 * @author		Tinsh
 * @copyright	(C) 2005-2015 Kilofox Studio
 */
class RoleModel extends Model
{
	private $_values = NULL;
	private $_loaded = false;
	protected $tableName = 'roles';
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
	 * 根据主键加载数据，并返回对象
	 * @return	对象
	 */
	public function load($id = 0)
	{
		if ( is_numeric($id) && $id > 0 )
		{
			$this->_values = DB::table('nodes')->select('*')->where('id', '=', $id)->where('author_id', '=', 1)->first();
			$this->_loaded = true;
		}
		return $this->_values;
	}
}