<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Illuminate\Database\Grammar;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;

class SQLiteDumper extends PostgresDumper
{
    protected function getGrammar(): Grammar
    {
        return new SQLiteGrammar();
    }

    protected function escape(string $value): string
    {
        return str_replace(["'"], ["''"], $value);
    }
}
