<?php

namespace Recca0120\EloquentDumper\Output;

use Recca0120\EloquentDumper\Dumper;

class ConsoleOutput implements OutputInterface
{
    private static $callable;

    public static function setEchoFunction(callable $callable): void
    {
        static::$callable = $callable;
    }

    public function output(string $sql): void
    {
        $callable = is_callable(static::$callable) ? static::$callable : [static::class, 'defaultEchoFunction'];
        $callable(Dumper::format($sql));
    }

    private static function defaultEchoFunction($sql): void
    {
        fwrite(STDOUT, $sql);
    }
}
