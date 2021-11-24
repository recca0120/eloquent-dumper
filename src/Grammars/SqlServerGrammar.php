<?php

namespace Recca0120\EloquentDumper\Grammars;

class SqlServerGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['[', ']']);
    }
}
