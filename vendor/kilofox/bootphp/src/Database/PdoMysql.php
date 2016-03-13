<?php

namespace Bootphp\Database;
use Bootphp\Database\Database;
use Bootphp\Database\Result\ResultCached;
use Bootphp\ExceptionHandler;
use Bootphp\Profiler\Profiler;
/**
 *  PDO_MySQL 数据库连接。
 *
 * @package BootPHP/数据库
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class PdoMysql extends Database
{
	// PDO_MySQL 使用反引号作为标识符
	protected $_identifier = '`';
	public function __construct($name, array $config)
	{
		parent::__construct($name, $config);
		if ( isset($this->_config['identifier']) )
		{
			// Allow the identifier to be overloaded per-connection
			$this->_identifier = (string)$this->_config['identifier'];
		}
	}
	public function connect()
	{
		if ( $this->_connection )
			return;
		// 提取连接参数，添加必要的变量
		extract($this->_config['connection'] + array(
			'dsn' => '',
			'username' => NULL,
			'password' => NULL,
			'persistent' => FALSE,
		));
		// 出于安全考虑，清除连接参数
		unset($this->_config['connection']);
		// 强制 PDO 对所有错误使用异常
		$options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
		if ( !empty($persistent) )
		{
			// 使连接持久化
			$options[\PDO::ATTR_PERSISTENT] = TRUE;
		}
		try
		{
			// 创建一个新的 PDO 连接
			$this->_connection = new \PDO($dsn, $username, $password, $options);
		}
		catch( PDOException $e )
		{
			throw new ExceptionHandler(':error', array(':error' => $e->getMessage()), $e->getCode());
		}
		if ( !empty($this->_config['charset']) )
		{
			// 设置字符集
			$this->setCharset($this->_config['charset']);
		}
	}
	public function disconnect()
	{
		// 销毁 PDO 对象
		$this->_connection = NULL;
		return parent::disconnect();
	}
	public function setCharset($charset)
	{
		// 确保数据库已连接
		$this->_connection or $this->connect();
		$this->_connection->exec('SET NAMES ' . $this->quote($charset));
	}
	public function query($type, $sql)
	{
		// 确保数据库已连接
		$this->_connection or $this->connect();
		if ( $this->_config['profiling'] )
		{
			// 对当前实例的查询实施基准测试
			$benchmark = Profiler::start("数据库（{$this->_instance}）", $sql);
		}
		try
		{
			$result = $this->_connection->query($sql);
		}
		catch( Exception $e )
		{
			if ( isset($benchmark) )
			{
				// 基准测试已无价值
				Profiler::delete($benchmark);
			}
			// 将异常转换为数据库异常
			throw new ExceptionHandler(':error [ :query ]', array(
			':error' => $e->getMessage(),
			':query' => $sql
			), $e->getCode());
		}
		if ( isset($benchmark) )
		{
			Profiler::stop($benchmark);
		}
		// 设置最后一次查询
		$this->last_query = $sql;
		if ( $type === 'select' )
		{
			$result->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
			$result = $result->fetchAll();
			// 返回结果的迭代
			return new ResultCached($result, $sql);
		}
		elseif ( $type === 'insert' )
		{
			// 返回插入的 ID 与创建行数的列表
			return array(
				$this->_connection->lastInsertId(),
				$result->rowCount(),
			);
		}
		else
		{
			// 返回受影响的行数
			return $result->rowCount();
		}
	}
	public function begin($mode = NULL)
	{
		// 确保数据库已连接
		$this->_connection or $this->connect();
		return $this->_connection->beginTransaction();
	}
	public function commit()
	{
		// 确保数据库已连接
		$this->_connection or $this->connect();
		return $this->_connection->commit();
	}
	public function rollback()
	{
		// 确保数据库已连接
		$this->_connection or $this->connect();
		return $this->_connection->rollBack();
	}
	/**
	 * 用转义字符清理可导致SQL注入攻击的字符串。
	 *
	 * @param	string	要转义的值
	 * @return	string
	 */
	public function escape($value)
	{
		// 确保数据库已连接
		$this->_connection or $this->connect();
		return $this->_connection->quote($value);
	}
}