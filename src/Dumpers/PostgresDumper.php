<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Recca0120\EloquentDumper\Dumper;

class PostgresDumper extends Dumper
{
    /**
     * @param string $sql
     * @return string
     */
    protected function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['"', '"']);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function escape(string $value): string
    {
        return str_replace(["'", '\\'], ["''", '\\\\'], $value);
    }
}
