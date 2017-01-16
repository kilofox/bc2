<?php

defined('SYS_PATH') or exit('Access Denied.');

return [
    'default' => [
        'type' => 'mysql',
        'connection' => [
            /**
             * The following options are available for PDO_MYSQL:
             *
             * string	dsn			Data Source Name
             * string	username	Database username
             * string	password	Database password
             * boolean	persistent	Use persistent connections?
             */
            'dsn' => 'mysql:host=localhost;dbname=bootphp',
            'username' => 'root',
            'password' => 'root',
            'persistent' => false,
        ],
        /**
         * The following extra options are available for PDO_MYSQL:
         *
         * string	identifier	Set the escaping identifier
         */
        'tablePrefix' => 'bc_',
        'charset' => 'utf8',
        'caching' => false,
    ],
    'alternate' => [
        'type' => 'oracle',
        'connection' => [
            'dsn' => 'oci:dbname=bootphp',
            'username' => false,
            'password' => false,
            'persistent' => false,
            'ssl' => null,
        ],
        'tablePrefix' => '',
        'charset' => 'utf8',
        'caching' => false,
    ]
];
