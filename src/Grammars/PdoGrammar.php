<?php

namespace Recca0120\EloquentDumper\Grammars;

class PdoGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize($sql)
    {
        return $sql;
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
