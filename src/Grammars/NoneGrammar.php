<?php

namespace Recca0120\EloquentDumper\Grammars;

class NoneGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['', '']);
    }

    /**
     * @param string $value
     * @return string
     */
    public function parameterize(string $value): string
    {
        return $this->quoteString($value);
    }
}
