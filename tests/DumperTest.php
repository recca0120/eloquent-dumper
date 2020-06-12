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
    public function test_it_should_get_simple_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper();

        $query->from('users')
            ->where('name', 'foo')
            ->where('password', 'bar');

        $this->assertSql($expected, $dumper, $query);
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
    public function test_it_should_get_condition_in_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper();

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
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
    public function test_it_should_convert_to_none_quote_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::NONE);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
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
    public function test_it_should_convert_to_mysql_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::MYSQL);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
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
    public function test_it_should_convert_to_sqlite_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::SQLITE);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
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
    public function test_it_should_convert_to_postgres_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::POSTGRES);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
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
    public function test_it_should_convert_to_mssql_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::MSSQL);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
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

    /**
     * @param string $expected
     * @param StubDumper $dumper
     * @param Builder $query
     */
    private function assertSql(string $expected, StubDumper $dumper, Builder $query)
    {
        $this->assertEquals(
            $expected,
            $dumper->dump($query->toSql(), $query->getBindings()),
            $this->getDriver($query)
        );
    }
}
