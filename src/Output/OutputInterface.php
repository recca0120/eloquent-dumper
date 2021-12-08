<?php

namespace Recca0120\EloquentDumper\Output;

interface OutputInterface
{
    public function dump(string $sql): void;
}
