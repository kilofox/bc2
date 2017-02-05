<?php

/**
 * Database reader for the kohana config system
 *
 * @package    Bootphp/Database
 * @category   Configuration
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (c) 2012 Kohana Team
 * @license    http://kilofox.net/license
 */
class Kohana_Config_Database_Reader implements Bootphp\Config\Reader
{
    protected $_db_instance;
    protected $_table_name = 'config';

    /**
     * Constructs the database reader object
     *
     * @param array Configuration for the reader
     */
    public function __construct(array $config = null)
    {
        if (isset($config['instance'])) {
            $this->_db_instance = $config['instance'];
        } elseif ($this->_db_instance === null) {
            $this->_db_instance = Database::$default;
        }

        if (isset($config['table_name'])) {
            $this->_table_name = $config['table_name'];
        }
    }

    /**
     * Tries to load the specificed configuration group
     *
     * Returns false if group does not exist or an array if it does
     *
     * @param  string $group Configuration group
     * @return boolean|array
     */
    public function load($group)
    {
        /**
         * Prevents the catch-22 scenario where the database config reader attempts to load the
         * database connections details from the database.
         */
        if ($group === 'database')
            return false;

        $query = DB::select('config_key', 'config_value')
                ->from($this->_table_name)
                ->where('group_name', '=', $group)
                ->execute($this->_db_instance);

        return count($query) ? array_map('unserialize', $query->as_array('config_key', 'config_value')) : false;
    }

}
