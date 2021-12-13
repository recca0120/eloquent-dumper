<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Recca0120\EloquentDumper\Dumper;

class WithoutQuoteDumper extends Dumper
{
    /**
     * @param string $sql
     * @return string
     */
    protected function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['', '']);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function parameterize(string $value): string
    {
        return $this->quoteString($value);
    }
}
