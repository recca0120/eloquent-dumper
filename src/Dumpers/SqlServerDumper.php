<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Illuminate\Database\Grammar;
use Illuminate\Database\Query\Grammars\SqlServerGrammar;
use Recca0120\EloquentDumper\Dumper;

class SqlServerDumper extends Dumper
{
    protected function getGrammar(): Grammar
    {
        return new SqlServerGrammar();
    }

    protected function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['[', ']']);
    }
}
