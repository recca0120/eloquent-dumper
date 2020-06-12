<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\Query\Builder;
use Recca0120\EloquentDumper\Dumper;

class DumperTest extends TestCase
{
    /**
     * @dataProvider simpleProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_simple(Builder $query, $expected)
    {
        $dumper = $this->givenDumper();

        $query->from('users')
            ->where('name', 'foo')
            ->where('password', 'bar');

        $this->assertEquals($expected, $dumper->sql($query), $this->getDriver($query));
    }

    public function simpleProvider()
    {
        return [
            [$this->mysql(), 'select * from `users` where `name` = \'foo\' and `password` = \'bar\''],
            [$this->sqlite(), 'select * from "users" where "name" = \'foo\' and "password" = \'bar\''],
            [$this->postgres(), 'select * from "users" where "name" = \'foo\' and "password" = \'bar\''],
            [$this->sqlServer(), 'select * from [users] where [name] = \'foo\' and [password] = \'bar\''],
        ];
    }

    /**
     * @dataProvider inProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_in(Builder $query, $expected)
    {
        $dumper = $this->givenDumper();

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertEquals($expected, $dumper->sql($query), $this->getDriver($query));
    }

    public function inProvider()
    {
        return [
            [$this->mysql(), 'select * from `users` where `id` in (1, 2, 3, 4, 5)'],
            [$this->sqlite(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
            [$this->postgres(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
            [$this->sqlServer(), 'select * from [users] where [id] in (1, 2, 3, 4, 5)'],
        ];
    }

    /**
     * @dataProvider getNoneDriverProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_none_driver(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::NONE);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertEquals($expected, $dumper->sql($query), $this->getDriver($query));
    }

    public function getNoneDriverProvider()
    {
        return [
            [$this->mysql(), 'select * from users where id in (1, 2, 3, 4, 5)'],
            [$this->sqlite(), 'select * from users where id in (1, 2, 3, 4, 5)'],
            [$this->postgres(), 'select * from users where id in (1, 2, 3, 4, 5)'],
            [$this->sqlServer(), 'select * from users where id in (1, 2, 3, 4, 5)'],
        ];
    }

    /**
     * @dataProvider getMySQLDriverProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_mysql_driver(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::MYSQL);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertEquals($expected, $dumper->sql($query), $this->getDriver($query));
    }

    public function getMySQLDriverProvider()
    {
        return [
            [$this->mysql(), 'select * from `users` where `id` in (1, 2, 3, 4, 5)'],
            [$this->sqlite(), 'select * from `users` where `id` in (1, 2, 3, 4, 5)'],
            [$this->postgres(), 'select * from `users` where `id` in (1, 2, 3, 4, 5)'],
            [$this->sqlServer(), 'select * from `users` where `id` in (1, 2, 3, 4, 5)'],
        ];
    }

    /**
     * @dataProvider getSqliteDriverProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_sqlite_driver(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::SQLITE);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertEquals($expected, $dumper->sql($query), $this->getDriver($query));
    }

    public function getSqliteDriverProvider()
    {
        return [
            [$this->mysql(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
            [$this->sqlite(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
            [$this->postgres(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
            [$this->sqlServer(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
        ];
    }

    /**
     * @dataProvider getPostgresDriverProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_postgres_driver(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::POSTGRES);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertEquals($expected, $dumper->sql($query), $this->getDriver($query));
    }

    public function getPostgresDriverProvider()
    {
        return [
            [$this->mysql(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
            [$this->sqlite(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
            [$this->postgres(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
            [$this->sqlServer(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
        ];
    }

    /**
     * @dataProvider getMsSQLDriverProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_mssql_driver(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::MSSQL);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertEquals($expected, $dumper->sql($query), $this->getDriver($query));
    }

    public function getMsSQLDriverProvider()
    {
        return [
            [$this->mysql(), 'select * from [users] where [id] in (1, 2, 3, 4, 5)'],
            [$this->sqlite(), 'select * from [users] where [id] in (1, 2, 3, 4, 5)'],
            [$this->postgres(), 'select * from [users] where [id] in (1, 2, 3, 4, 5)'],
            [$this->sqlServer(), 'select * from [users] where [id] in (1, 2, 3, 4, 5)'],
        ];
    }
}
