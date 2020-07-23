<?php

namespace Recca0120\EloquentDumper\Grammars;

class SQLiteGrammar extends PostgresGrammar
{
    protected function escape($value)
    {
        return str_replace(["'"], ["''"], $value);
    }
}
