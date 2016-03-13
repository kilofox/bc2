<?php

namespace App\articles\models;
use Bootphp\Model;
use Bootphp\Database\DB;
/**
 * 节点模型。
 *
 * @package		BootCMS
 * @category	模型
 * @author		Tinsh
 * @copyright	(C) 2005-2015 Kilofox Studio
 */
class ArticleModel extends Model
{
	private $_values = NULL;
	private $_loaded = false;
	protected $tableName = 'articles';
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
			$this->_values = DB::table($this->tableName)->select('*')->where('id', $id)->first();
			$this->_loaded = true;
		}
		return $this->_values;
	}
	/**
	 * 根据指定的分类取得文章
	 */
	public function findByCategory($cateId = 0)
	{
		if ( $cateId <= 0 )
			return NULL;
		$values = DB::select()->from($this->tableName)->where('category', '=', $cateId)->execute()->asArray();
		return $values;
	}
}