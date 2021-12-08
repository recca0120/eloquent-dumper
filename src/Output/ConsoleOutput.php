<?php

namespace Recca0120\EloquentDumper\Output;

class ConsoleOutput implements OutputInterface
{
    public function dump(string $sql): void
    {
        echo "\n".$sql."\n";
    }
}
