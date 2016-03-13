<?php

namespace Bootphp\Database;
use Bootphp\Database\Query;
use Bootphp\Database\Expression;
/**
 * 数据库连接封装。
 *
 * 你可以使用 `Database::instance('name')` 获得一个数据库实例，name 为 [config](database/config) 的一个组。
 *
 * 这个类通过数据库驱动提供对连接实例的管理，以及引括、转义和其它相关功能。
 * 查询由 [Database\Query] 和 [Database\Query\Builder] 对象来完成，用 [DB] 辅助类可以轻松创建。
 *
 * @package Bootphp/数据库
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
abstract class Database
{
	/**
	 * @var string 默认实例名
	 */
	public static $default = 'default';
	/**
	 * @var array 数据库实例
	 */
	public static $instances = array();
	/**
	 * 取得 Database 单例。
	 *
	 *     // 加载默认数据库
	 *     $db = Database::instance();
	 *
	 *     // 由指定的配置名的创建一个实例
	 *     $db = Database::instance('自定义');
	 *
	 * @param	string	$name 实例名
	 * @return	Database
	 */
	public static function instance($name = NULL)
	{
		if ( $name === NULL )
		{
			// 使用默认实例名
			$name = self::$default;
		}
		if ( !isset(self::$instances[$name]) )
		{
			// 加载数据库配置
			$configs = require APP_PATH . '/../configs/database.php';
			$config = $configs[$name];
			if ( !isset($config['type']) )
			{
				throw new \Bootphp\Exception\ExceptionHandler('在 ' . $name . ' 配置中没有定义数据库类型');
			}
			// 设置驱动的类名
			$driver = 'Bootphp\\Database\\' . 'Pdo' . ucfirst($config['type']);
			// 创建数据库连接实例
			$driver = new $driver($name, $config);
			// 存储数据库实例
			self::$instances[$name] = $driver;
		}
		return self::$instances[$name];
	}
	/**
	 * @var string 最后一次执行的查询
	 */
	public $last_query;
	// 用于引括标识符的字符
	protected $_identifier = '"';
	// 实例名
	protected $_instance;
	// 原生的服务器连接
	protected $_connection;
	// 配置数组
	protected $_config;
	/**
	 * 本地存储数据库配置，并命名实例。
	 *
	 * [!!] 这个方法不能直接访问，必须使用 [Database::instance]。
	 *
	 * @return	void
	 */
	public function __construct($name, array $config)
	{
		// 设置实例名
		$this->_instance = $name;
		// 存储配置于本地
		$this->_config = $config;
		if ( empty($this->_config['table_prefix']) )
		{
			$this->_config['table_prefix'] = '';
		}
	}
	/**
	 * 对象销毁时，断开与数据库的连接。
	 *
	 *     // 销毁数据库实例
	 *     unset(Database::instances[(string)$db]);
	 *
	 * [!!] 调用 `unset($db)` 不足以销毁数据库，因为它仍然存储于 `Database::$instances`。
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		$this->disconnect();
	}
	/**
	 * 返回数据库实例名。
	 *
	 *     echo (string)$db;
	 *
	 * @return	string
	 */
	public function __toString()
	{
		return $this->_instance;
	}
	/**
	 * 连接数据库。第一次查询执行时，会自动调用。
	 *
	 *     $db->connect();
	 *
	 * @throws ExceptionHandler
	 * @return	void
	 */
	abstract public function connect();
	/**
	 * 断开数据库连接。由 [Database::__destruct] 自动调用。
	 * 从 [Database::$instances] 中清除数据库实例。
	 *
	 *     $db->disconnect();
	 *
	 * @return	boolean
	 */
	public function disconnect()
	{
		unset(self::$instances[$this->_instance]);
		return true;
	}
	/**
	 * 设置连接字符集。由 [Database::connect] 自动调用。
	 *
	 *     $db->setCharset('utf8');
	 *
	 * @throws ExceptionHandler
	 * @param	string	$charset 字符集名
	 * @return	void
	 */
	abstract public function setCharset($charset);
	/**
	 * 执行给定的类型的 SQL 查询。
	 *
	 *     // Make a SELECT query and use objects for results
	 *     $db->query('select', 'SELECT * FROM groups', true);
	 *
	 *     // Make a SELECT query and use "Model_User" for the results
	 *     $db->query('select', 'SELECT * FROM users LIMIT 1', 'Model_User');
	 *
	 * @param	integer	$type 'select', 'insert', etc
	 * @param	string	$sql SQL query
	 * @return	object   Database_Result for SELECT queries
	 * @return	array list (insert id, row count) for INSERT queries
	 * @return	integer  number of affected rows for all other queries
	 */
	abstract public function query($type, $sql);
	/**
	 * 开始 SQL 事务
	 *
	 *     // 开始事务
	 *     $db->begin();
	 *
	 *     try
	 *     {
	 *          DB::insert('users')->values($user1)...
	 *          DB::insert('users')->values($user2)...
	 *          // 插入成功，提交更改
	 *          $db->commit();
	 *     }
	 *     catch( Database_Exception $e )
	 *     {
	 *          // 插入失败，回滚更改
	 *          $db->rollback();
	 *     }
	 *
	 * @param	string	$mode 事务模式
	 * @return	boolean
	 */
	abstract public function begin($mode = NULL);
	/**
	 * 提交当前事务
	 *
	 *     // 提交数据库更改
	 *     $db->commit();
	 *
	 * @return	boolean
	 */
	abstract public function commit();
	/**
	 * 停止当前事务
	 *
	 *     // 撤销更改
	 *     $db->rollback();
	 *
	 * @return	boolean
	 */
	abstract public function rollback();
	/**
	 * Returns a normalized array describing the SQL data type
	 *
	 *     $db->datatype('char');
	 *
	 * @param	string	$type SQL data type
	 * @return	array
	 */
	public function datatype($type)
	{
		static $types = array
			(
			// SQL-92
			'bit' => array('type' => 'string', 'exact' => true),
			'bit varying' => array('type' => 'string'),
			'char' => array('type' => 'string', 'exact' => true),
			'char varying' => array('type' => 'string'),
			'character' => array('type' => 'string', 'exact' => true),
			'character varying' => array('type' => 'string'),
			'date' => array('type' => 'string'),
			'dec' => array('type' => 'float', 'exact' => true),
			'decimal' => array('type' => 'float', 'exact' => true),
			'double precision' => array('type' => 'float'),
			'float' => array('type' => 'float'),
			'int' => array('type' => 'int', 'min' => '-2147483648', 'max' => '2147483647'),
			'integer' => array('type' => 'int', 'min' => '-2147483648', 'max' => '2147483647'),
			'interval' => array('type' => 'string'),
			'national char' => array('type' => 'string', 'exact' => true),
			'national char varying' => array('type' => 'string'),
			'national character' => array('type' => 'string', 'exact' => true),
			'national character varying' => array('type' => 'string'),
			'nchar' => array('type' => 'string', 'exact' => true),
			'nchar varying' => array('type' => 'string'),
			'numeric' => array('type' => 'float', 'exact' => true),
			'real' => array('type' => 'float'),
			'smallint' => array('type' => 'int', 'min' => '-32768', 'max' => '32767'),
			'time' => array('type' => 'string'),
			'time with time zone' => array('type' => 'string'),
			'timestamp' => array('type' => 'string'),
			'timestamp with time zone' => array('type' => 'string'),
			'varchar' => array('type' => 'string'),
			// SQL:1999
			'binary large object' => array('type' => 'string', 'binary' => true),
			'blob' => array('type' => 'string', 'binary' => true),
			'boolean' => array('type' => 'bool'),
			'char large object' => array('type' => 'string'),
			'character large object' => array('type' => 'string'),
			'clob' => array('type' => 'string'),
			'national character large object' => array('type' => 'string'),
			'nchar large object' => array('type' => 'string'),
			'nclob' => array('type' => 'string'),
			'time without time zone' => array('type' => 'string'),
			'timestamp without time zone' => array('type' => 'string'),
			// SQL:2003
			'bigint' => array('type' => 'int', 'min' => '-9223372036854775808', 'max' => '9223372036854775807'),
			// SQL:2008
			'binary' => array('type' => 'string', 'binary' => true, 'exact' => true),
			'binary varying' => array('type' => 'string', 'binary' => true),
			'varbinary' => array('type' => 'string', 'binary' => true),
		);
		if ( isset($types[$type]) )
			return $types[$type];
		return array();
	}
	/**
	 * Extracts the text between parentheses, if any.
	 *
	 *     // Returns: array('CHAR', '6')
	 *     list($type, $length) = $db->_parse_type('CHAR(6)');
	 *
	 * @param	string	$type
	 * @return	array list containing the type and length, if any
	 */
	protected function _parse_type($type)
	{
		if ( ($open = strpos($type, '(')) === false )
		{
			// No length specified
			return array($type, NULL);
		}
		// Closing parenthesis
		$close = strrpos($type, ')', $open);
		// Length without parentheses
		$length = substr($type, $open + 1, $close - 1 - $open);
		// Type without the length
		$type = substr($type, 0, $open) . substr($type, $close + 1);
		return array($type, $length);
	}
	/**
	 * Return the table prefix defined in the current configuration.
	 *
	 *     $prefix = $db->table_prefix();
	 *
	 * @return	string
	 */
	public function table_prefix()
	{
		return $this->_config['table_prefix'];
	}
	/**
	 * Quote a value for an SQL query.
	 *
	 *     $db->quote(NULL);   // 'NULL'
	 *     $db->quote(10);     // 10
	 *     $db->quote('fred'); // 'fred'
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Expression] objects will be compiled.
	 * [Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param mixed $value any value to quote
	 * @return	string
	 * @uses    Database::escape
	 */
	public function quote($value)
	{
		if ( $value === NULL )
		{
			return 'NULL';
		}
		elseif ( $value === true )
		{
			return "'1'";
		}
		elseif ( $value === false )
		{
			return "'0'";
		}
		elseif ( is_object($value) )
		{
			if ( $value instanceof Query )
			{
				// Create a sub-query
				return '(' . $value->compile($this) . ')';
			}
			elseif ( $value instanceof Expression )
			{
				// Compile the expression
				return $value->compile($this);
			}
			else
			{
				// Convert the object to a string
				return $this->quote((string)$value);
			}
		}
		elseif ( is_array($value) )
		{
			return '(' . implode(', ', array_map(array($this, __FUNCTION__), $value)) . ')';
		}
		elseif ( is_int($value) )
		{
			return (int)$value;
		}
		elseif ( is_float($value) )
		{
			// Convert to non-locale aware float to prevent possible commas
			return sprintf('%F', $value);
		}
		return $this->escape($value);
	}
	/**
	 * Quote a database column name and add the table prefix if needed.
	 *
	 *     $column = $db->quoteColumn($column);
	 *
	 * You can also use SQL methods within identifiers.
	 *
	 *     $column = $db->quoteColumn(DB::expr('COUNT(`column`)'));
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Expression] objects will be compiled.
	 * [Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param mixed $column column name or array(column, alias)
	 * @return	string
	 * @uses    Database::quote_identifier
	 * @uses    Database::table_prefix
	 */
	public function quoteColumn($column)
	{
		// Identifiers are escaped by repeating them
		$escaped_identifier = $this->_identifier . $this->_identifier;
		if ( is_array($column) )
		{
			list($column, $alias) = $column;
			$alias = str_replace($this->_identifier, $escaped_identifier, $alias);
		}
		if ( $column instanceof Query )
		{
			// Create a sub-query
			$column = '(' . $column->compile($this) . ')';
		}
		elseif ( $column instanceof Expression )
		{
			// Compile the expression
			$column = $column->compile($this);
		}
		else
		{
			// Convert to a string
			$column = (string)$column;
			$column = str_replace($this->_identifier, $escaped_identifier, $column);
			if ( $column === '*' )
			{
				return $column;
			}
			elseif ( strpos($column, '.') !== false )
			{
				$parts = explode('.', $column);
				if ( $prefix = $this->table_prefix() )
				{
					// Get the offset of the table name, 2nd-to-last part
					$offset = count($parts) - 2;
					// Add the table prefix to the table name
					$parts[$offset] = $prefix . $parts[$offset];
				}
				foreach( $parts as & $part )
				{
					if ( $part !== '*' )
					{
						// Quote each of the parts
						$part = $this->_identifier . $part . $this->_identifier;
					}
				}
				$column = implode('.', $parts);
			}
			else
			{
				$column = $this->_identifier . $column . $this->_identifier;
			}
		}
		if ( isset($alias) )
		{
			$column .= ' AS ' . $this->_identifier . $alias . $this->_identifier;
		}
		return $column;
	}
	/**
	 * Quote a database table name and adds the table prefix if needed.
	 *
	 *     $table = $db->quoteTable($table);
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Expression] objects will be compiled.
	 * [Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param mixed $table table name or array(table, alias)
	 * @return	string
	 * @uses    Database::quote_identifier
	 * @uses    Database::table_prefix
	 */
	public function quoteTable($table)
	{
		// Identifiers are escaped by repeating them
		$escaped_identifier = $this->_identifier . $this->_identifier;
		if ( is_array($table) )
		{
			list($table, $alias) = $table;
			$alias = str_replace($this->_identifier, $escaped_identifier, $alias);
		}
		if ( $table instanceof Database_Query )
		{
			// Create a sub-query
			$table = '(' . $table->compile($this) . ')';
		}
		elseif ( $table instanceof Expression )
		{
			// Compile the expression
			$table = $table->compile($this);
		}
		else
		{
			// Convert to a string
			$table = (string)$table;
			$table = str_replace($this->_identifier, $escaped_identifier, $table);
			if ( strpos($table, '.') !== false )
			{
				$parts = explode('.', $table);
				if ( $prefix = $this->table_prefix() )
				{
					// Get the offset of the table name, last part
					$offset = count($parts) - 1;
					// Add the table prefix to the table name
					$parts[$offset] = $prefix . $parts[$offset];
				}
				foreach( $parts as & $part )
				{
					// Quote each of the parts
					$part = $this->_identifier . $part . $this->_identifier;
				}
				$table = implode('.', $parts);
			}
			else
			{
				// Add the table prefix
				$table = $this->_identifier . $this->table_prefix() . $table . $this->_identifier;
			}
		}
		if ( isset($alias) )
		{
			// Attach table prefix to alias
			$table .= ' AS ' . $this->_identifier . $this->table_prefix() . $alias . $this->_identifier;
		}
		return $table;
	}
	/**
	 * Quote a database identifier
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Expression] objects will be compiled.
	 * [Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param mixed $value any identifier
	 * @return	string
	 */
	public function quote_identifier($value)
	{
		// Identifiers are escaped by repeating them
		$escaped_identifier = $this->_identifier . $this->_identifier;
		if ( is_array($value) )
		{
			list($value, $alias) = $value;
			$alias = str_replace($this->_identifier, $escaped_identifier, $alias);
		}
		if ( $value instanceof Database_Query )
		{
			// Create a sub-query
			$value = '(' . $value->compile($this) . ')';
		}
		elseif ( $value instanceof Expression )
		{
			// Compile the expression
			$value = $value->compile($this);
		}
		else
		{
			// Convert to a string
			$value = (string)$value;
			$value = str_replace($this->_identifier, $escaped_identifier, $value);
			if ( strpos($value, '.') !== false )
			{
				$parts = explode('.', $value);
				foreach( $parts as & $part )
				{
					// Quote each of the parts
					$part = $this->_identifier . $part . $this->_identifier;
				}
				$value = implode('.', $parts);
			}
			else
			{
				$value = $this->_identifier . $value . $this->_identifier;
			}
		}
		if ( isset($alias) )
		{
			$value .= ' AS ' . $this->_identifier . $alias . $this->_identifier;
		}
		return $value;
	}
	/**
	 * 用转义字符清理可导致SQL注入攻击的字符串。
	 *
	 *     $value = $db->escape('任何字符串');
	 *
	 * @param	string	$value 要转义的值
	 * @return	string
	 */
	abstract public function escape($value);
}