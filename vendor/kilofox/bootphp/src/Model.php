<?php

namespace Bootphp;
use Bootphp\Database\DB;
/**
 * 模型基类。所有的模型都应该继承这个类。
 *
 * @package BootPHP
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
abstract class Model
{
	protected $db = NULL;
	protected $tableName = '';
	public function __construct()
	{
		$this->db = DB::select()->from($this->tableName);
	}
	/**
	 * 创建一个新的模型实例
	 *
	 *     $model = Model::factory($name);
	 *
	 * @param	string	模型名
	 * @return	模型
	 */
	public static function factory($name, $application = 'core')
	{
		// 添加模型后缀
		$class = 'App\\' . $application . '\\models\\' . ucfirst($name) . 'Model';
		if ( !class_exists($class) )
		{
			throw new \Bootphp\Exception\ExceptionHandler('类 ' . $class . ' 不存在。');
		}
		return new $class;
	}
	/**
	 * 找到指定条件的一行
	 *
	 * @param mixed $options 查询条件
	 * @return	object 加载的对象
	 */
	public function find($options)
	{
		if ( is_int($options) )
		{
			return $this->db->where('id', '=', $options)->execute()->get();
		}
		if ( isset($options[2]) )
		{
			return $this->db->where($options[0], $options[1], $options[2])->execute()->get();
		}
		if ( isset($options[1]) )
		{
			return $this->db->where($options[0], '=', $options[1])->execute()->get();
		}
		return NULL;
	}
	/**
	 * 找到指定条件的所有行
	 *
	 * @param	integer	$id 主键ID
	 * @return	object 加载的对象
	 */
	public function findAll($options = array())
	{
		if ( isset($options[2]) )
		{
			return $this->db->where($options[0], $options[1], $options[2])->execute()->asArray();
		}
		if ( isset($options[1]) )
		{
			return $this->db->where($options[0], '=', $options[1])->execute()->asArray();
		}
		return $this->db->execute()->asArray();
	}
	/**
	 * 排序
	 */
	public function order($column, $direction = NULL)
	{
		$this->db->orderBy($column, $direction);
		return $this;
	}
	/**
	 * 排序
	 */
	public function limit($limit)
	{
		$this->db->limit($limit);
		return $this;
	}
	/**
	 * 统计总数
	 *
	 * @return	integer 数值
	 */
	public function count()
	{
		//$count = $this->db->select([DB::expr('COUNT(1)'), 'count'])->execute();
		return isset($count['count']) ? $count['count'] : 0;
	}
	/**
	 * 创建信息
	 *
	 * @param array $data 要创建的数据
	 * @return	mixed 创建结果或影响的行数
	 */
	public function create($data = array())
	{
		if ( !is_array($data) || !$data )
			return false;
		$result = DB::insert($this->tableName, array_keys($data))->values(array_values($data))->execute();
		return $result;
	}
	/**
	 * 删除信息
	 *
	 * @param array $data 要删除的数据
	 * @return	mixed 创建结果或影响的行数
	 */
	public function delete($data = array())
	{
		if ( !is_array($data) || count($data) <> 3 )
			return false;
		$result = DB::delete($this->tableName)->where($data[0], $data[1], $data[2])->execute();
		return $result;
	}
	/**
	 * 更新信息
	 *
	 * @param array $data 要更新的数据
	 * @param array $options 查询条件
	 * @return	mixed 更新结果或影响的行数
	 */
	public function update($data = array(), $options = array())
	{
		if ( !is_array($data) || !is_array($options) || !$data || !isset($options[2]) )
			return false;
		$result = DB::update($this->tableName)->set($data)->where($options[0], $options[1], $options[2])->execute();
		return $result;
	}
	/**
	 * 分页列表
	 */
	public function findPage()
	{
		$pager = \Bootphp\Pagination::factory(array(
				'total_items' => $articles->count_all(),
				'items_per_page' => 4,
				'first_page_in_url' => TURE,
		));
	}
}