<?php

return [
    /*
     * Supported: "default", "mysql", "sqlite", "pgsql", "sqlsrv"
     */
    'grammar' => env('ELOQUENT_DUMPER_GRAMMAR', 'default'),
    'logging' => [
        'format' => '[%connection-name%] [%time%] %sql% | %method% %uri%',
        'channels' => [
            'log' => [
                'driver' => 'single',
                'path' => storage_path('logs/sql.log'),
                'level' => 'debug',
            ],
            'slow-log' => [
                'driver' => 'single',
                'path' => storage_path('logs/slow-sql.log'),
                'level' => 'debug',
            ],
        ],
    ],
];
