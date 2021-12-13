<?php

namespace Recca0120\EloquentDumper\Output;

interface OutputInterface
{
    public function output(string $sql): void;
}
