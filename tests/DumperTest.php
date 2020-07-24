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

        $query->from('users')->whereIn('id', [1, null, false, 'foo']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function inProvider()
    {
        return [
            [$this->mysql(), 'select * from `users` where `id` in (1, NULL, 0, \'foo\')'],
            [$this->sqlite(), 'select * from "users" where "id" in (1, NULL, 0, \'foo\')'],
            [$this->postgres(), 'select * from "users" where "id" in (1, NULL, 0, \'foo\')'],
            [$this->sqlServer(), 'select * from [users] where [id] in (1, NULL, 0, \'foo\')'],
        ];
    }

    /**
     * @dataProvider noneGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_none_quote_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::NONE);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
    }

    public function noneGrammarProvider()
    {
        return [
            [$this->mysql(), 'select * from users where id in (1, 2, 3, 4, 5)'],
            [$this->sqlite(), 'select * from users where id in (1, 2, 3, 4, 5)'],
            [$this->postgres(), 'select * from users where id in (1, 2, 3, 4, 5)'],
            [$this->sqlServer(), 'select * from users where id in (1, 2, 3, 4, 5)'],
        ];
    }

    /**
     * @dataProvider mySQLGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_mysql_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::MYSQL);

        $query->from('users')->whereIn('id', [1, 2, 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function mySQLGrammarProvider()
    {
        return [
            [$this->mysql(), "select * from `users` where `id` in (1, 2, 'App\\\\User', 'don\'t be late')"],
            [$this->sqlite(), "select * from `users` where `id` in (1, 2, 'App\\\\User', 'don\'t be late')"],
            [$this->postgres(), "select * from `users` where `id` in (1, 2, 'App\\\\User', 'don\'t be late')"],
            [$this->sqlServer(), "select * from `users` where `id` in (1, 2, 'App\\\\User', 'don\'t be late')"],
        ];
    }

    /**
     * @dataProvider sqliteGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_sqlite_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::SQLITE);

        $query->from('users')->whereIn('id', [1, 2, 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function sqliteGrammarProvider()
    {
        return [
            [$this->mysql(), 'select * from "users" where "id" in (1, 2, \'App\\User\', \'don\'\'t be late\')'],
            [$this->sqlite(), 'select * from "users" where "id" in (1, 2, \'App\\User\', \'don\'\'t be late\')'],
            [$this->postgres(), 'select * from "users" where "id" in (1, 2, \'App\\User\', \'don\'\'t be late\')'],
            [$this->sqlServer(), 'select * from "users" where "id" in (1, 2, \'App\\User\', \'don\'\'t be late\')'],
        ];
    }

    /**
     * @dataProvider postgresGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_postgres_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::POSTGRES);

        $query->from('users')->whereIn('id', [1, 2, 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function postgresGrammarProvider()
    {
        return [
            [$this->mysql(), 'select * from "users" where "id" in (1, 2, \'App\\\\User\', \'don\'\'t be late\')'],
            [$this->sqlite(), 'select * from "users" where "id" in (1, 2, \'App\\\\User\', \'don\'\'t be late\')'],
            [$this->postgres(), 'select * from "users" where "id" in (1, 2, \'App\\\\User\', \'don\'\'t be late\')'],
            [$this->sqlServer(), 'select * from "users" where "id" in (1, 2, \'App\\\\User\', \'don\'\'t be late\')'],
        ];
    }

    /**
     * @dataProvider sqlServerGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_mssql_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::MSSQL);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
    }

    /**
     * @dataProvider sqlServerGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_sqlserver_version_sql(Builder $query, $expected)
    {
        $dumper = $this->givenDumper(Dumper::SQLSERVER);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
    }

    public function sqlServerGrammarProvider()
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
    private function assertSql($expected, StubDumper $dumper, Builder $query)
    {
        $this->assertEquals(
            $expected,
            $dumper->dump($query->toSql(), $query->getBindings()),
            $this->getDriver($query)
        );
    }
}
