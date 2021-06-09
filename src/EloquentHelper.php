<?php

namespace Recca0120\EloquentDumper;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class EloquentHelper
{
    /**
     * @var Dumper
     */
    private $dumper;
    /**
     * @var Application
     */
    private $app;

    /**
     * EloquentHelper constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->dumper = $app->get(Dumper::class);
    }

    /**
     * @param QueryBuilder|Builder $query
     * @return string
     */
    public function toRawSql($query, $format = false)
    {
        $this->dumper->setPdo($query->getConnection()->getPdo());

        return $this->dumper->dump($query->toSql(), $query->getBindings(), $format);
    }

    /**
     * @param QueryBuilder|Builder $query
     * @return Builder|QueryBuilder
     */
    public function dumpSql($query)
    {
        $sql = $this->toRawSql($query, true);

        if ($this->app->runningInConsole()) {
            echo "\n".$sql."\n";

            return $query;
        }

        function_exists('dump') ? dump($sql) : var_dump($sql);

        return $query;
    }

    public function getRawQueryLog($logs = [])
    {
        return array_map(function ($log) {
            return [
                'query' => $this->dumper->dump($log['query'], $log['bindings'], false),
                'time' => $log['time'],
            ];
        }, empty($logs) ? DB::getQueryLog() : $logs);
    }
}
