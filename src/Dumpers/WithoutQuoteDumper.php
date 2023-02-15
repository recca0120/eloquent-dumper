<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Recca0120\EloquentDumper\Dumper;

class WithoutQuoteDumper extends Dumper
{
    protected function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['', '']);
    }

    protected function parameterize(string $value): string
    {
        return $this->quoteString($value);
    }
}
