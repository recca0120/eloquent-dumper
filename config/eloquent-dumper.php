<?php

return [
    /*
     * Supported: "default", "mysql", "sqlite", "pgsql", "sqlsrv"
     */
    'grammar' => env('ELOQUENT_DUMPER_GRAMMAR', 'default'),
    'logging' => [
        'channel' => [
            'driver' => 'single',
            'path' => storage_path('logs/sql.log'),
            'level' => 'debug',
        ],
    ],
];
