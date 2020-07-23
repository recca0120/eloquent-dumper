<?php

namespace Recca0120\EloquentDumper\Driver;

class PDO extends Driver
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
