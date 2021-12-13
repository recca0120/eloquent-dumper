<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Recca0120\EloquentDumper\Dumper;

class DumperTest extends TestCase
{
    /**
     * @dataProvider simpleProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_get_simple_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper();

        $query->from('users')
            ->where('name', 'foo')
            ->where('password', 'bar');

        $this->assertSql($expected, $dumper, $query);
    }

    public function simpleProvider(): array
    {
        return [[
            $this->mysql(),
            'select * from `users` where `name` = \'foo\' and `password` = \'bar\'',
        ], [
            $this->sqlite(),
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            $this->postgres(),
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            $this->sqlServer(),
            'select * from [users] where [name] = \'foo\' and [password] = \'bar\'',
        ]];
    }

    /**
     * @dataProvider whereRawProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_get_where_raw_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper();
        $grammar = $query->getGrammar();

        $query->from('users')->whereRaw('? = ? and ? = ?', [
            new Expression($grammar->wrap('name')),
            'foo',
            new Expression($grammar->wrap('password')),
            'bar',
        ]);

        $this->assertSql($expected, $dumper, $query);
    }

    public function whereRawProvider(): array
    {
        return [[
            $this->mysql(),
            'select * from `users` where `name` = \'foo\' and `password` = \'bar\'',
        ], [
            $this->sqlite(),
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            $this->postgres(),
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            $this->sqlServer(),
            'select * from [users] where [name] = \'foo\' and [password] = \'bar\'',
        ]];
    }

    /**
     * @dataProvider inProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_get_condition_in_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper();

        $query->from('users')->whereIn('id', [new Expression(1), null, false, 'foo']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function inProvider(): array
    {
        return [[
            $this->mysql(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\')',
        ], [
            $this->sqlite(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\')',
        ], [
            $this->postgres(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\')',
        ], [
            $this->sqlServer(),
            'select * from [users] where [id] in (1, NULL, 0, \'foo\')',
        ]];
    }

    /**
     * @dataProvider withoutQuoteGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_without_quote_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::WITHOUT_QUOTE);

        $query->from('users')->whereIn('id', [1, null, false, 'foo']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function withoutQuoteGrammarProvider(): array
    {
        return [[
            $this->mysql(),
            'select * from users where id in (1, NULL, 0, \'foo\')',
        ], [
            $this->sqlite(),
            'select * from users where id in (1, NULL, 0, \'foo\')',
        ], [
            $this->postgres(),
            'select * from users where id in (1, NULL, 0, \'foo\')',
        ], [
            $this->sqlServer(),
            'select * from users where id in (1, NULL, 0, \'foo\')',
        ]];
    }

    /**
     * @dataProvider mySQLGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_mysql_version_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::MYSQL);

        $query->from('users')->whereIn('id', [1, null, false, 'foo', 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function mySQLGrammarProvider(): array
    {
        return [[
            $this->mysql(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\\\'t be late\')',
        ], [
            $this->sqlite(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\\\'t be late\')',
        ], [
            $this->postgres(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\\\'t be late\')',
        ], [
            $this->sqlServer(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\\\'t be late\')',
        ]];
    }

    /**
     * @dataProvider sqliteGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_sqlite_version_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::SQLITE);

        $query->from('users')->whereIn('id', [1, null, false, 'foo', 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function sqliteGrammarProvider(): array
    {
        return [[
            $this->mysql(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\User\', \'don\'\'t be late\')',
        ], [
            $this->sqlite(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\User\', \'don\'\'t be late\')',
        ], [
            $this->postgres(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\User\', \'don\'\'t be late\')',
        ], [
            $this->sqlServer(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\User\', \'don\'\'t be late\')',
        ]];
    }

    /**
     * @dataProvider postgresGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_postgres_version_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::POSTGRES);

        $query->from('users')->whereIn('id', [1, null, false, 'foo', 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public function postgresGrammarProvider(): array
    {
        return [[
            $this->mysql(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\'\'t be late\')',
        ], [
            $this->sqlite(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\'\'t be late\')',
        ], [
            $this->postgres(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\'\'t be late\')',
        ], [
            $this->sqlServer(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\'\'t be late\')',
        ]];
    }

    /**
     * @dataProvider sqlServerGrammarProvider
     * @param Builder $query
     * @param string $expected
     */
    public function test_it_should_convert_to_sqlserver_version_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::SQLSERVER);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
    }

    public function sqlServerGrammarProvider(): array
    {
        return [[
            $this->mysql(),
            'select * from [users] where [id] in (1, 2, 3, 4, 5)',
        ], [
            $this->sqlite(),
            'select * from [users] where [id] in (1, 2, 3, 4, 5)',
        ], [
            $this->postgres(),
            'select * from [users] where [id] in (1, 2, 3, 4, 5)',
        ], [
            $this->sqlServer(),
            'select * from [users] where [id] in (1, 2, 3, 4, 5)',
        ]];
    }

    /**
     * @param string $expected
     * @param Dumper $dumper
     * @param Builder $query
     */
    private function assertSql(string $expected, Dumper $dumper, Builder $query): void
    {
        self::assertEquals(
            $expected,
            $dumper->dump($query->toSql(), $query->getBindings(), false),
            $this->getDriver($query)
        );
    }
}
