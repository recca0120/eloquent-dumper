<?php

namespace Recca0120\EloquentDumper;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Recca0120\EloquentDumper\Output\OutputInterface;
use Recca0120\EloquentDumper\Output\VarDumpOutput;

class EloquentHelper
{
    /**
     * @var Dumper
     */
    private $dumper;
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param Dumper $dumper
     * @param OutputInterface|null $output
     */
    public function __construct(Dumper $dumper, ?OutputInterface $output = null)
    {
        $this->dumper = $dumper;
        $this->output = $output ?: new VarDumpOutput();
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
    public function dumpSql($query)
    {
        $this->output->dump($this->toRawSql($query, true));

        return $query;
    }
}
