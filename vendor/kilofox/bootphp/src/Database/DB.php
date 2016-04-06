<?php

namespace Bootphp\Database;
use Bootphp\Database\Expression;
use Bootphp\Database\Query\Builder\Select;
use Bootphp\Database\Query\Builder\Insert;
use Bootphp\Database\Query\Builder\Update;
use Bootphp\Database\Query\Builder\Delete;
/**
 * Provides a shortcut to get Database related objects for [making queries](../database/query).
 *
 * Shortcut     | Returned Object
 * -------------|---------------
 * [`DB::query()`](#query)   | [Database_Query]
 * [`DB::insert()`](#insert) | [Database_Query_Builder_Insert]
 * [`DB::select()`](#select),<br />[`DB::selectArray()`](#selectArray) | [Database_Query_Builder_Select]
 * [`DB::update()`](#update) | [Database_Query_Builder_Update]
 * [`DB::delete()`](#delete) | [Database_Query_Builder_Delete]
 * [`DB::expr()`](#expr)     | [Database_Expression]
 *
 * You pass the same parameters to these functions as you pass to the objects they return.
 *
 * @package	   Kohana/Database
 * @category	  Base
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class DB
{
	/**
	 * Create a new [Database_Query] of the given type.
	 *
	 *     // Create a new SELECT query
	 *     $query = DB::query('select', 'SELECT * FROM users');
	 *
	 *     // Create a new DELETE query
	 *     $query = DB::query('delete', 'DELETE FROM users WHERE id = 5');
	 *
	 * Specifying the type changes the returned result. When using
	 * `'select'`, a [Database_Query_Result] will be returned.
	 * `'insert'` queries will return the insert id and number of rows.
	 * For all other queries, the number of affected rows is returned.
	 *
	 * @param	integer	$type type: 'select', 'update', etc
	 * @param	string	$sql SQL statement
	 * @return	Database_Query
	 */
	public static function query($type, $sql)
	{
		return new Database_Query($type, $sql);
	}
	/**
	 * Create a new [Database_Query_Builder_Select]. Each argument will be
	 * treated as a column. To generate a `foo AS bar` alias, use an array.
	 *
	 *     // SELECT id, username
	 *     $query = DB::select('id', 'username');
	 *
	 *     // SELECT id AS user_id
	 *     $query = DB::select(array('id', 'user_id'));
	 *
	 * @param mixed $columns column name or array($column, $alias) or object
	 * @return	Database_Query_Builder_Select
	 */
	public static function select($columns = NULL)
	{
		return new Select(func_get_args());
	}
	/**
	 * Create a new [Database_Query_Builder_Select] from an array of columns.
	 *
	 *     // SELECT id, username
	 *     $query = DB::select_array(array('id', 'username'));
	 *
	 * @param array $columns columns to select
	 * @return	Database_Query_Builder_Select
	 */
	public static function selectArray(array $columns = NULL)
	{
		return new Database_Query_Builder_Select($columns);
	}
	/**
	 * Create a new [Database_Query_Builder_Insert].
	 *
	 *     // INSERT INTO users (id, username)
	 *     $query = DB::insert('users', array('id', 'username'));
	 *
	 * @param	string	$table table to insert into
	 * @param array $columns list of column names or array($column, $alias) or object
	 * @return	Database_Query_Builder_Insert
	 */
	public static function insert($table = NULL, array $columns = NULL)
	{
		return new Insert($table, $columns);
	}
	/**
	 * Create a new [Database_Query_Builder_Update].
	 *
	 *     // UPDATE users
	 *     $query = DB::update('users');
	 *
	 * @param	string	$table table to update
	 * @return	Database_Query_Builder_Update
	 */
	public static function update($table = NULL)
	{
		return new Update($table);
	}
	/**
	 * Create a new [Database_Query_Builder_Delete].
	 *
	 *     // DELETE FROM users
	 *     $query = DB::delete('users');
	 *
	 * @param	string	$table table to delete from
	 * @return	Database_Query_Builder_Delete
	 */
	public static function delete($table = NULL)
	{
		return new Delete($table);
	}
	/**
	 * Create a new [Database_Expression] which is not escaped. An expression
	 * is the only way to use SQL functions within query builders.
	 *
	 *     $expression = DB::expr('COUNT(users.id)');
	 *     $query = DB::update('users')->set(array('login_count' => DB::expr('login_count + 1')))->where('id', '=', $id);
	 *     $users = ORM::factory('user')->where(DB::expr("BINARY `hash`"), '=', $hash)->find();
	 *
	 * @param	string	$string expression
	 * @param array  parameters
	 * @return	Database_Expression
	 */
	public static function expr($string, $parameters = array())
	{
		return new Expression($string, $parameters);
	}
}