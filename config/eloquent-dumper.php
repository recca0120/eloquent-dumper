<?php

return [
    /*
     * Supported: "pdo", "mysql", "sqlite", "postgres", "mssql"
     */
    'grammar' => env('ELOQUENT_DUMPER_DRIVER', 'pdo'),
];
