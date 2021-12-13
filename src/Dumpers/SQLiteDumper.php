<?php

namespace Recca0120\EloquentDumper\Dumpers;

class SQLiteDumper extends PostgresDumper
{
    protected function escape(string $value): string
    {
        return str_replace(["'"], ["''"], $value);
    }
}
