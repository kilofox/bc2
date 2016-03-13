<?php

namespace Application\core\models;
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
class NodeModel extends Model
{
	private $_values = NULL;
	private $_loaded = false;
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
	/**
	 * 取得所有单页
	 */
	public function findAll($order_by = '')
	{
		$result = [];
		$order_by = $order_by ? $order_by : 'created DESC';
		//$query = DB::insert('nodes', ['node_title', 'node_intro', 'node_content'])->values(['tttttttt', 'iiiiii', 'ccccccccccccccccccc']);
		//$result = $query->execute();
		//$query = DB::update('nodes')->set(['author_id' => 2])->where('id', '=', 3);
		//$result = $query->execute();
		//$query = DB::delete('nodes')->where('id', '=', 8);
		//$result = $query->execute();
		//$query = DB::select('n.*')->from(['nodes', 'n'])->join(['users', 'u'], 'left')->on('n.author_id', '=', 'u.id')->where('n.id', '<', 5)->groupBy('n.commenting')->having('n.author_id', '>=', 0)->orderBy('n.id', ' desc')->limit(5)->offset(0);
		$query = DB::select('n.*')->cached(null, true)->from(['nodes', 'n'])->join(['users', 'u'], 'left')->on('n.author_id', '=', 'u.id')->where('n.id', '<', 5)->having('n.author_id', '>=', 0)->orderBy('n.id', ' desc')->limit(5)->offset(0);
		$result = $query->execute()->as_array();
		//print_r($count);
		return $result;
	}
}