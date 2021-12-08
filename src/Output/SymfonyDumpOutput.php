<?php

namespace Recca0120\EloquentDumper\Output;

class SymfonyDumpOutput implements OutputInterface
{
    public function dump(string $sql): void
    {
        dump($sql);
    }
}
