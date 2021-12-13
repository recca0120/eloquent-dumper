<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;
use Illuminate\Database\Query\Grammars\SqlServerGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor;
use Illuminate\Database\Query\Processors\PostgresProcessor;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Query\Processors\SQLiteProcessor;
use Illuminate\Database\Query\Processors\SqlServerProcessor;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase as BaseCase;
use Recca0120\EloquentDumper\Dumper;

abstract class TestCase extends BaseCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @param Builder $query
     * @return string
     */
    protected function getDriver(Builder $query): string
    {
        return get_class($query->getGrammar());
    }

    /**
     * @return Builder
     */
    protected function mysql(): Builder
    {
        return new Builder($this->mockConnection(
            new MySqlGrammar(),
            new MySqlProcessor()
        ));
    }

    /**
     * @return Builder
     */
    protected function sqlite(): Builder
    {
        return new Builder($this->mockConnection(
            new SqliteGrammar(),
            new SqliteProcessor()
        ));
    }

    /**
     * @return Builder
     */
    protected function sqlServer(): Builder
    {
        return new Builder($this->mockConnection(
            new SqlServerGrammar(),
            new SqlServerProcessor()
        ));
    }

    /**
     * @return Builder
     */
    protected function postgres(): Builder
    {
        return new Builder($this->mockConnection(
            new PostgresGrammar(),
            new PostgresProcessor()
        ));
    }

    /**
     * @param string|null $grammar
     * @return Dumper
     */
    protected function givenDumper(?string $grammar = null): Dumper
    {
        return Dumper::factory($grammar);
    }

    /**
     * @param Grammar $grammar
     * @param Processor $processor
     * @return ConnectionInterface
     */
    private function mockConnection(Grammar $grammar, Processor $processor): ConnectionInterface
    {
        $connection = m::mock(Connection::class);
        $connection->allows('getQueryGrammar')->andReturns($grammar);
        $connection->allows('getPostProcessor')->andReturns($processor);

        return $connection;
    }
}
