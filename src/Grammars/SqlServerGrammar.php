<?php

namespace Recca0120\EloquentDumper\Grammars;

class SqlServerGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize($sql)
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['[', ']']);
    }
}
