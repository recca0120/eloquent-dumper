<?php

namespace Recca0120\EloquentDumper\Parser;

class Defaults extends Parser
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize($sql)
    {
        return $sql;
    }
}
