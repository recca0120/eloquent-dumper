<?php

namespace Recca0120\EloquentDumper\Output;

use Recca0120\EloquentDumper\Dumper;

class ConsoleOutput implements OutputInterface
{
    public function output(string $sql): void
    {
        echo "\n".Dumper::format($sql)."\n";
    }
}
