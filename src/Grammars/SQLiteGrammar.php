<?php

namespace Recca0120\EloquentDumper\Grammars;

class SQLiteGrammar extends PostgresGrammar
{
    protected function escape(string $value): string
    {
        return str_replace(["'"], ["''"], $value);
    }
}
