<?php

namespace Recca0120\EloquentDumper\Dumpers;

use Recca0120\EloquentDumper\Dumper;

class PdoDumper extends Dumper
{
    /**
     * @param  string  $sql
     * @return string
     */
    protected function columnize(string $sql): string
    {
        return $sql;
    }

    /**
     * @param  string  $value
     * @return string
     */
    protected function parameterize(string $value): string
    {
        return $this->pdo ? $this->pdo->quote($value) : parent::parameterize(addslashes($value));
    }
}
