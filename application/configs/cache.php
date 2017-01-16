<?php

defined('SYS_PATH') or exit('Access Denied.');

return [
    'memcache' => [
        'driver' => 'memcache',
        'defaultExpire' => 3600,
        'compression' => false, // Use Zlib compression (can cause issues with integers)
        'servers' => [
            'local' => [
                'host' => 'localhost', // Memcache Server
                'port' => 11211, // Memcache port number
                'persistent' => false, // Persistent connection
                'weight' => 1,
                'timeout' => 1,
                'retryInterval' => 15,
                'status' => true,
            ],
        ],
        'instantDeath' => true, // Take server offline immediately on first fail (no retry)
    ],
    'apc' => [
        'driver' => 'apc',
        'defaultExpire' => 3600,
    ],
    'file' => [
        'driver' => 'file',
        'cacheDir' => APP_PATH . '/cache',
        'defaultExpire' => 3600,
        'ignoreOnDelete' => [
            '.gitignore',
            '.git',
            '.svn'
        ]
    ]
];
