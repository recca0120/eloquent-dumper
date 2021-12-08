<?php

namespace Recca0120\EloquentDumper;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class EloquentHelper
{
    /**
     * @var Dumper
     */
    private $dumper;

    /**
     * EloquentHelper constructor.
     * @param Dumper $dumper
     */
    public function __construct(Dumper $dumper)
    {
        $this->dumper = $dumper;
    }

    /**
     * @param QueryBuilder|Builder $query
     * @param bool $format
     * @return string
     */
    public function toRawSql($query, bool $format = false): string
    {
        $this->dumper->setPdo($query->getConnection()->getPdo());

        return $this->dumper->dump($query->toSql(), $query->getBindings(), $format);
    }

    /**
     * @param QueryBuilder|Builder $query
     * @return Builder|QueryBuilder
     */
    public function dumpSql($query, $runningInConsole = false)
    {
        $sql = $this->toRawSql($query, true);

        if ($runningInConsole) {
            echo "\n".$sql."\n";

            return $query;
        }

        self::dump($sql);

        return $query;
    }

    private static function dump(string $sql): void
    {
        $dump = function_exists('dump') ? 'dump' : 'var_dump';
        $dump($sql);
    }
}
