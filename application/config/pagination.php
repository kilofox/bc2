<?php

defined('SYS_PATH') or exit('Access Denied.');

return [
    // Application defaults
    'default' => [
        'currentPage' => ['source' => 'query_string', 'key' => 'page'], // source: "query_string" or "route"
        'totalItems' => 0,
        'itemsPerPage' => 2,
        'view' => 'default',
        'autoHide' => true,
        'firstPageInUrl' => false,
    ],
];
