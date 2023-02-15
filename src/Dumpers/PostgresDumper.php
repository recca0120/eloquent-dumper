<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Illuminate\Database\Grammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Recca0120\EloquentDumper\Dumper;

class PostgresDumper extends Dumper
{
    protected function getGrammar(): Grammar
    {
        return new PostgresGrammar();
    }

    protected function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['"', '"']);
    }

    protected function escape(string $value): string
    {
        return str_replace(["'", '\\'], ["''", '\\\\'], $value);
    }
}
