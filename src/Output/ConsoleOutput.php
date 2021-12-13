<?php

namespace Recca0120\EloquentDumper\Output;

use Doctrine\SqlFormatter\SqlFormatter;

class ConsoleOutput implements OutputInterface
{
    public function output(string $sql): void
    {
        echo "\n".self::format($sql)."\n";
    }

    /**
     * @param string $sql
     * @return string
     */
    private static function format(string $sql): string
    {
        return (new SqlFormatter())->format($sql);
    }
}
