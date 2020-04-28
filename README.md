# Eloquent Dumper

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
