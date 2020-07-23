<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Query\Grammars\SQLiteGrammar;
use Illuminate\Database\Query\Grammars\SqlServerGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor;
use Illuminate\Database\Query\Processors\PostgresProcessor;
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
     * @return false|string
     */
    protected function getDriver(Builder $query)
    {
        return get_class($query->getGrammar());
    }

    /**
     * @return Builder
     */
    protected function mysql()
    {
        $connection = m::mock(ConnectionInterface::class);
        $connection->shouldReceive('getQueryGrammar')->andReturn(new MySqlGrammar());
        $connection->shouldReceive('getPostProcessor')->andReturn(new MySqlProcessor());

        return new Builder($connection);
    }

    /**
     * @return Builder
     */
    protected function sqlite()
    {
        $connection = m::mock(ConnectionInterface::class);
        $connection->shouldReceive('getQueryGrammar')->andReturn(new SqliteGrammar());
        $connection->shouldReceive('getPostProcessor')->andReturn(new SqliteProcessor());

        return new Builder($connection);
    }

    /**
     * @return Builder
     */
    protected function sqlServer()
    {
        $connection = m::mock(ConnectionInterface::class);
        $connection->shouldReceive('getQueryGrammar')->andReturn(new SqlServerGrammar());
        $connection->shouldReceive('getPostProcessor')->andReturn(new SqlServerProcessor());

        return new Builder($connection);
    }

    /**
     * @return Builder
     */
    protected function postgres()
    {
        $connection = m::mock(ConnectionInterface::class);
        $connection->shouldReceive('getQueryGrammar')->andReturn(new PostgresGrammar());
        $connection->shouldReceive('getPostProcessor')->andReturn(new PostgresProcessor());

        return new Builder($connection);
    }

    /**
     * @param string|null $driver
     * @return StubDumper
     */
    protected function givenDumper($driver = null)
    {
        return (new StubDumper())->setDriver($driver ?: Dumper::PDO);
    }
}

class StubDumper extends Dumper
{
    protected function format($sql)
    {
        return $sql;
    }
}
