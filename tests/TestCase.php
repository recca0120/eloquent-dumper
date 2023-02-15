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

    protected function getDriver(Builder $query): string
    {
        return get_class($query->getGrammar());
    }

    protected static function mysql(): Builder
    {
        return new Builder(static::mockConnection(
            new MySqlGrammar(),
            new MySqlProcessor()
        ));
    }

    protected static function sqlite(): Builder
    {
        return new Builder(static::mockConnection(
            new SqliteGrammar(),
            new SqliteProcessor()
        ));
    }

    protected static function sqlServer(): Builder
    {
        return new Builder(static::mockConnection(
            new SqlServerGrammar(),
            new SqlServerProcessor()
        ));
    }

    protected static function postgres(): Builder
    {
        return new Builder(static::mockConnection(
            new PostgresGrammar(),
            new PostgresProcessor()
        ));
    }

    protected function givenDumper(?string $driver = null): Dumper
    {
        return Dumper::factory($driver);
    }

    private static function mockConnection(Grammar $grammar, Processor $processor): ConnectionInterface
    {
        $connection = m::mock(Connection::class);
        $connection->allows('getQueryGrammar')->andReturns($grammar);
        $connection->allows('getPostProcessor')->andReturns($processor);

        return $connection;
    }
}
