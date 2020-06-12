# Eloquent Dumper

## Install

composer install
```bash
composer require recca0120/eloquent-dumper
```

publish config
```bash
php artisan vendor:publish --tag="eloquent-dumper"
```

## Config

when you use sqlite in PHPUnit and you need MySQL version sql, you can set driver to mysql, it will output MySQL version sql

```php
// eloquent-dumper.php
return [
    // Supported: "default", "mysql", "sqlite", "postgres", "mssql"
    'driver' => env('ELOQUENT_DUMPER_DRIVER', 'mysql'),
];
```
## How to use

```php
var_dump(
    User::where('name', 'foo')
        ->where('password', 'bar')
        ->sql()
);

User::where('name', 'foo')
    ->where('password', 'bar')
    ->dumpSql()
    ->get();

// output:
// SELECT
//     *
// FROM
//     `users`
// WHERE
//     `name` = 'foo' AND `password` = 'bar'
```
