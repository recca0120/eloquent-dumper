<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Recca0120\EloquentDumper\Dumper;

class MySqlDumper extends Dumper
{
    /**
     * @param string $sql
     * @return string
     */
    protected function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['`', '`']);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function escape(string $value): string
    {
        $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'];

        return str_replace($search, $replace, $value);
    }
}
