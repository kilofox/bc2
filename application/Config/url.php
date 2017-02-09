<?php

defined('SYS_PATH') or exit('Access Denied.');

return [
    'trustedHosts' => [
        /**
         * Set up your hostnames here.
         *
         * Example:
         *
         *     'example\.org',
         *     '.*\.example\.org',
         *
         * Do not forget to escape your dots (.) as these are regex patterns.
         * These patterns should always fully match, as they are prepended
         * with `^` and appended with `$`.
         */
        'localhost',
    ],
];
