<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Recca0120\EloquentDumper\Dumper;

class SqlServerDumper extends Dumper
{
    /**
     * @param string $sql
     * @return string
     */
    protected function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['[', ']']);
    }
}
