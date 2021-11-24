<?php

namespace Recca0120\EloquentDumper\Grammars;

class PostgresGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize(string $sql): string
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
