<?php

namespace Bootphp\Database;
use Bootphp\Database\Database;
use Bootphp\Database\Result\ResultCached;
use Bootphp\ExceptionHandler;
use Bootphp\Profiler\Profiler;
/**
 *  PDO database connection.
 *
 * @package	BootPHP/Database
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class PdoMysql extends Database
{
	// PDO_MYSQL uses no quoting for identifiers
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

		// Extract the connection parameters, adding required variabels
		extract($this->_config['connection'] + array(
			'dsn' => '',
			'username' => NULL,
			'password' => NULL,
			'persistent' => FALSE,
		));

		// Clear the connection parameters for security
		unset($this->_config['connection']);

		// Force PDO to use exceptions for all errors
		$options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;

		if ( !empty($persistent) )
		{
			// Make the connection persistent
			$options[\PDO::ATTR_PERSISTENT] = TRUE;
		}

		try
		{
			// Create a new PDO connection
			$this->_connection = new \PDO($dsn, $username, $password, $options);
		}
		catch( PDOException $e )
		{
			throw new ExceptionHandler(':error', array(':error' => $e->getMessage()), $e->getCode());
		}

		if ( !empty($this->_config['charset']) )
		{
			// Set the character set
			$this->setCharset($this->_config['charset']);
		}
	}
	public function disconnect()
	{
		// Destroy the PDO object
		$this->_connection = NULL;
		return parent::disconnect();
	}
	public function setCharset($charset)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();
		$this->_connection->exec('SET NAMES ' . $this->quote($charset));
	}
	public function query($type, $sql)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ( $this->_config['profiling'] )
		{
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("database({$this->_instance})", $sql);
		}

		try
		{
			$result = $this->_connection->query($sql);
		}
		catch( Exception $e )
		{
			if ( isset($benchmark) )
			{
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			// Convert the exception in a database exception
			throw new ExceptionHandler(':error [ :query ]', array(
			':error' => $e->getMessage(),
			':query' => $sql
			), $e->getCode());
		}
		if ( isset($benchmark) )
		{
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if ( $type === 'select' )
		{
			$result->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
			$result = $result->fetchAll();

			// Return an iterator of results
			return new ResultCached($result, $sql);
		}
		elseif ( $type === 'insert' )
		{
			// Return a list of insert id and rows created
			return array(
				$this->_connection->lastInsertId(),
				$result->rowCount(),
			);
		}
		else
		{
			// Return the number of rows affected
			return $result->rowCount();
		}
	}
	public function begin($mode = NULL)
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();
		return $this->_connection->beginTransaction();
	}
	public function commit()
	{
		// Make sure the database is connected
		$this->_connection or $this->connect();
		return $this->_connection->commit();
	}
	public function rollback()
	{
		// Make sure the database is connected
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
		// Make sure the database is connected
		$this->_connection or $this->connect();
		return $this->_connection->quote($value);
	}
}