<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Illuminate\Database\Grammar;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Recca0120\EloquentDumper\Dumper;

class MySqlDumper extends Dumper
{
    protected function getGrammar(): Grammar
    {
        return new MySqlGrammar();
    }

    protected function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['`', '`']);
    }

    protected function escape(string $value): string
    {
        $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'];

        return str_replace($search, $replace, $value);
    }
}
