<?php

namespace Recca0120\EloquentDumper\Grammars;

class PostgresGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize($sql)
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['"', '"']);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function escape($value)
    {
        return str_replace(["'", '\\'], ["''", '\\\\'], $value);
    }
}
