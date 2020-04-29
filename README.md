# Eloquent Dumper

## Install
```bash
composer require recca0120/eloquent-dumper
```

## How to use

```php
var_dump(
    User::where('name', 'foo')
        ->where('password', 'bar')
        ->rawSql()
);

User::where('name', 'foo')
    ->where('password', 'bar')
    ->dumpRawSql()
    ->get();

// output:
// SELECT
//     *
// FROM
//     `users`
// WHERE
//     `name` = 'foo' AND `password` = 'bar'
```
