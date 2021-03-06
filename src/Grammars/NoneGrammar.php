<?php

namespace Recca0120\EloquentDumper\Grammars;

class NoneGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize($sql)
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['', '']);
    }

    /**
     * @param string $value
     * @return string
     */
    public function parameterize($value)
    {
        return $this->quoteString($value);
    }
}
