<?php

namespace Recca0120\EloquentDumper\Parser;

class Postgres extends Parser
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize($sql)
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['"', '"']);
    }
}
