<?php

namespace Recca0120\EloquentDumper\Parser;

class MySql extends Parser
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize($sql)
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['`', '`']);
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
