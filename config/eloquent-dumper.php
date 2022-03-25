<?php

return [
    /*
     * Supported: "default", "mysql", "sqlite", "pgsql", "sqlsrv"
     */
    'grammar' => env('ELOQUENT_DUMPER_GRAMMAR', 'default'),
    'logging' => [
        'format' => "\nurl: %uri%\nmethod: %method%\nconnection: %connection-name%\ntime: %time%\nquery: %formatted_sql%",
        'pattern' => '#(select).*#i',
        'slow_query_exec_time' => env('ELOQUENT_DUMPER_SLOW_QUERY_EXEC_TIME', 3),
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
