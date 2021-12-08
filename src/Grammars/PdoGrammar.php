<?php

namespace Recca0120\EloquentDumper\Grammars;

class PdoGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize(string $sql): string
    {
        return $sql;
    }

    /**
     * @param string $value
     * @return string
     */
    public function parameterize(string $value): string
    {
        return self::$pdo ? self::$pdo->quote($value) : parent::parameterize(addslashes($value));
    }
}
