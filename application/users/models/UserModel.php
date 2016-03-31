<?php

namespace App\users\models;
use Bootphp\Model;
use Bootphp\Database\DB;
/**
 * 用户模型。
 *
 * @package		BootCMS
 * @category	模型
 * @author		Tinsh
 * @copyright	(C) 2005-2015 Kilofox Studio
 */
class UserModel extends Model
{
	private $_values = NULL;
	private $_loaded = false;
	protected $tableName = 'users';
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
	 * 用户列表
	 */
	public function userList($itemsPerPage = 10, $baseUrl = '')
	{
		$list = ['data' => NULL];
		$users = Model::factory('user', 'users')->findAll();
		foreach( $users as &$node )
		{
			$node->created = \Bootphp\Date::unixToHuman($node->created);
			$node->operation = '<a href="' . $baseUrl . '/users/admin/' . $node->id . '/edit">编辑</a>';
		}
		$pager = \Bootphp\Pagination::factory(array(
				'total_items' => count($users),
				'items_per_page' => $itemsPerPage,
				'first_page_in_url' => true,
				'view' => 'metro'
		));
		$list['data'] = $users;
		$list['pager'] = $pager->render();
		return $list;
	}
}