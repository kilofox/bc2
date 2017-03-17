<?php

namespace Bootphp\Database\Database;

use Bootphp\BootphpException;
use Bootphp\Profiler;

/**
 * PDO database connection.
 *
 * @package    Bootphp/Database
 * @category   Drivers
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class PdoMysql extends \Bootphp\Database\Database
{
    // MySQL uses a backtick for identifiers
    protected $identifier = '`';

    public function __construct($name, array $config)
    {
        parent::__construct($name, $config);

        if (isset($this->config['identifier'])) {
            // Allow the identifier to be overloaded per-connection
            $this->identifier = (string) $this->config['identifier'];
        }
    }

    public function connect()
    {
        if ($this->connection)
            return;

        // Extract the connection parameters, adding required variabels
        extract($this->config['connection'] + [
            'dsn' => '',
            'username' => null,
            'password' => null,
            'persistent' => false,
        ]);

        // Clear the connection parameters for security
        unset($this->config['connection']);

        // Force PDO to use exceptions for all errors
        $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
        $options[\PDO::ATTR_EMULATE_PREPARES] = false;

        if (!empty($persistent)) {
            // Make the connection persistent
            $options[\PDO::ATTR_PERSISTENT] = true;
        }

        try {
            // Create a new PDO connection
            $this->connection = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new BootphpException($e->getMessage(), $e->getCode());
        }

        if (!empty($this->config['charset'])) {
            // Set the character set
            $this->setCharset($this->config['charset']);
        }
    }

    public function disconnect()
    {
        // Destroy the PDO object
        $this->connection = null;

        return parent::disconnect();
    }

    public function setCharset($charset)
    {
        // Make sure the database is connected
        $this->connection or $this->connect();

        // This SQL-92 syntax is not supported by all drivers
        $this->connection->exec('SET NAMES ' . $this->quote($charset));
    }

    public function query($type, $sql, $asObject = true)
    {
        // Make sure the database is connected
        $this->connection or $this->connect();

        if (\Bootphp\Core::$profiling) {
            // Benchmark this query for the current instance
            $benchmark = Profiler::start("Database ({$this->instance})", $sql);
        }

        try {
            $result = $this->connection->query($sql);
        } catch (\Exception $e) {
            if (isset($benchmark)) {
                // This benchmark is worthless
                Profiler::delete($benchmark);
            }

            // Convert the exception in a database exception
            throw new BootphpException($e->getMessage() . ' [ ' . $sql . ' ]', $e->getCode());
        }

        if (isset($benchmark)) {
            Profiler::stop($benchmark);
        }

        // Set the last query
        $this->lastQuery = $sql;

        if ($type === 'select') {
            if ($asObject === false) {
                $result->setFetchMode(\PDO::FETCH_ASSOC);
            } else {
                $result->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
            }

            $result = $result->fetchAll();

            // Return an iterator of results
            return new Result\Cached($result, $sql, $asObject);
        } elseif ($type === 'insert') {
            // Return a list of insert id and rows created
            return array(
                $this->connection->lastInsertId(),
                $result->rowCount(),
            );
        } else {
            // Return the number of rows affected
            return $result->rowCount();
        }
    }

    public function begin($mode = null)
    {
        // Make sure the database is connected
        $this->connection or $this->connect();

        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        // Make sure the database is connected
        $this->connection or $this->connect();

        return $this->connection->commit();
    }

    public function rollback()
    {
        // Make sure the database is connected
        $this->connection or $this->connect();

        return $this->connection->rollBack();
    }

    public function listTables($like = null)
    {
        if (is_string($like)) {
            // Search for table names
            $result = $this->query('select', 'SHOW TABLES LIKE ' . $this->quote($like), false);
        } else {
            // Find all table names
            $result = $this->query('select', 'SHOW TABLES', false);
        }

        $tables = array();
        foreach ($result as $row) {
            $tables[] = reset($row);
        }

        return $tables;
    }

    public function listColumns($table, $addPrefix = true)
    {
        // Quote the table name
        $table = $addPrefix === true ? $this->quoteTable($table) : $table;

        // Find all column names
        $result = $this->query('select', 'SHOW COLUMNS FROM ' . $table, false);

        $count = 0;
        $columns = [];
        foreach ($result as $row) {
            $columns[$row['Field']] = $row['Type'];
        }

        return $columns;
    }

    public function escape($value)
    {
        // Make sure the database is connected
        $this->connection or $this->connect();

        return $this->connection->quote($value);
    }

}
