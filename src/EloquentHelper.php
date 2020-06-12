<?php

namespace Recca0120\EloquentDumper;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class EloquentHelper
{
    /**
     * @var Dumper
     */
    private $dumper;
    /**
     * @var bool
     */
    private $runningInConsole;
    /**
     * @var Application
     */
    private $app;

    /**
     * EloquentHelper constructor.
     * @param Dumper $dumper
     * @param Application $app
     */
    public function __construct(Dumper $dumper, Application $app)
    {
        $this->dumper = $dumper;
        $this->app = $app;
    }

    /**
     * @param QueryBuilder|Builder $query
     * @return string
     */
    public function sql($query)
    {
        return $this->dumper->dump($query->toSql(), $query->getBindings());
    }

    /**
     * @param QueryBuilder|Builder $query
     * @return Builder|QueryBuilder
     */
    public function dump($query)
    {
        $sql = $this->sql($query);

        if ($this->app->runningInConsole()) {
            echo "\n".$sql."\n";

            return $query;
        }

        function_exists('dump') ? dump($sql) : var_dump($sql);

        return $query;
    }
}
