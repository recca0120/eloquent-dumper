<?php

namespace Recca0120\EloquentDumper\Output;

class VarDumpOutput implements OutputInterface
{
    public function output(string $sql): void
    {
        var_dump($sql);
    }
}
