<?php

namespace Recca0120\EloquentDumper\Output;

class SymfonyDumpOutput implements OutputInterface
{
    public function output(string $sql): void
    {
        dump($sql);
    }
}
