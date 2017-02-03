<?php

namespace Bootphp\Database;

/**
 * Provides a shortcut to get Database related objects for [making queries](../database/query).
 *
 * Shortcut     | Returned Object
 * -------------|---------------
 * [`DB::query()`](#query)   | [Database\Query]
 * [`DB::insert()`](#insert) | [Database\Query\Builder\Insert]
 * [`DB::select()`](#select),<br />[`DB::select_array()`](#select_array) | [Database\Query\Builder\Select]
 * [`DB::update()`](#update) | [Database\Query\Builder\Update]
 * [`DB::delete()`](#delete) | [Database\Query\Builder\Delete]
 * [`DB::expr()`](#expr)     | [Database\Expression]
 *
 * You pass the same parameters to these functions as you pass to the objects they return.
 *
 * @package    Bootphp/Database
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class DB
{
    /**
     * Create a new [Database\Query] of the given type.
     *
     *     // Create a new SELECT query
     *     $query = DB::query('select', 'SELECT * FROM users');
     *
     *     // Create a new DELETE query
     *     $query = DB::query('delete', 'DELETE FROM users WHERE id = 2');
     *
     * Specifying the type changes the returned result. When using `select`, a
     * [Database\Query\Result] will be returned.
     * `insert` queries will return the insert id and number of rows.
     * For all other queries, the number of affected rows is returned.
     *
     * @param   integer  $type  Type: 'select', 'update', etc
     * @param   string   $sql   SQL statement
     * @return  Database\Query
     */
    public static function query($type, $sql)
    {
        return new Database\Query($type, $sql);
    }

    /**
     * Create a new [Database\Query\Builder\Select]. Each argument will be
     * treated as a column. To generate a `foo AS bar` alias, use an array.
     *
     *     // SELECT id, username
     *     $query = DB::select('id', 'username');
     *
     *     // SELECT id AS user_id
     *     $query = DB::select(array('id', 'user_id'));
     *
     * @param   mixed   $columns    Column name or array($column, $alias) or object
     * @return  Database\Query\Builder\Select
     */
    public static function select($columns = null)
    {
        return new Database\Query\Builder\Select(func_get_args());
    }

    /**
     * Create a new [Database\Query\Builder\Select] from an array of columns.
     *
     *     // SELECT id, username
     *     $query = DB::select_array(array('id', 'username'));
     *
     * @param   array   $columns    Columns to select
     * @return  Database\Query\Builder\Select
     */
    public static function select_array(array $columns = null)
    {
        return new Database\Query\Builder\Select($columns);
    }

    /**
     * Create a new [Database\Query\Builder\Insert].
     *
     *     // INSERT INTO users (id, username)
     *     $query = DB::insert('users', array('id', 'username'));
     *
     * @param   string  $table      Table to insert into
     * @param   array   $columns    List of column names or array($column, $alias) or object
     * @return  Database\Query\Builder\Insert
     */
    public static function insert($table = null, array $columns = null)
    {
        return new Database\Query\Builder\Insert($table, $columns);
    }

    /**
     * Create a new [Database\Query\Builder\Update].
     *
     *     // UPDATE users
     *     $query = DB::update('users');
     *
     * @param   string  $table  Table to update
     * @return  Database\Query\Builder\Update
     */
    public static function update($table = null)
    {
        return new Database\Query\Builder\Update($table);
    }

    /**
     * Create a new [Database\Query\Builder\Delete].
     *
     *     // DELETE FROM users
     *     $query = DB::delete('users');
     *
     * @param   string  $table  Table to delete from
     * @return  Database\Query\Builder\Delete
     */
    public static function delete($table = null)
    {
        return new Database\Query\Builder\Delete($table);
    }

    /**
     * Create a new [Database\Expression] which is not escaped. An expression is
     * the only way to use SQL functions within query builders.
     *
     *     $expression = DB::expr('COUNT(users.id)');
     *     $query = DB::update('users')->set(array('login_count' => DB::expr('login_count + 1')))->where('id', '=', $id);
     *
     * @param   string  $string     Expression
     * @param   array   $parameters Parameters
     * @return  Database\Expression
     */
    public static function expr($string, $parameters = [])
    {
        return new Database\Expression($string, $parameters);
    }

}
