<?php

namespace Recca0120\EloquentDumper\Grammars;

class MySqlGrammar extends Grammar
{
    /**
     * @param string $sql
     * @return string
     */
    public function columnize(string $sql): string
    {
        return $this->replaceColumnQuotedIdentifiers($sql, ['`', '`']);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function escape(string $value): string
    {
        $search = ['\\', "\x00", "\n", "\r", "'", '"', "\x1a"];
        $replace = ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'];

        return str_replace($search, $replace, $value);
    }
}
