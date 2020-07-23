<?php

namespace Recca0120\EloquentDumper\Grammars;

class MySqlGrammar extends Grammar
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
    protected function escape($value)
    {
        $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'];

        return str_replace($search, $replace, $value);
    }
}
