<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\Grammar;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Mockery as m;
use Recca0120\EloquentDumper\Dumper;

class DumperTest extends TestCase
{
    /**
     * @dataProvider simpleProvider
     *
     * @param  Builder  $query
     * @param  string  $expected
     */
    public function test_it_should_get_simple_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper();

        $query->from('users')
            ->where('name', 'foo')
            ->where('password', 'bar');

        $this->assertSql($expected, $dumper, $query);
    }

    public static function simpleProvider(): array
    {
        return [[
            static::mysql(),
            'select * from `users` where `name` = \'foo\' and `password` = \'bar\'',
        ], [
            static::sqlite(),
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            static::postgres(),
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            static::sqlServer(),
            'select * from [users] where [name] = \'foo\' and [password] = \'bar\'',
        ]];
    }

    /**
     * @dataProvider whereRawProvider
     *
     * @param  Builder  $query
     * @param  string  $expected
     */
    public function test_it_should_get_where_raw_sql(Builder $query, string $expected): void
    {
        $grammar = $query->getGrammar();
        $driver = $this->getDriverFromGrammar($grammar);
        $dumper = $this->givenDumper($driver);

        $nameExpression = m::spy(new Expression($grammar->wrap('name')));
        $passwordExpression = m::spy(new Expression($grammar->wrap('password')));

        $query->from('users')->whereRaw('? = ? and ? = ?', [
            $nameExpression,
            'foo',
            $passwordExpression,
            'bar',
        ]);

        $this->assertSql($expected, $dumper, $query);

        $nameExpression
            ->shouldHaveReceived('getValue')
            ->with(m::on(function (Grammar $grammar) use ($driver) {
                return $this->getDriverFromGrammar($grammar) === $driver;
            }));
    }

    public static function whereRawProvider(): array
    {
        return [[
            static::mysql(),
            'select * from `users` where `name` = \'foo\' and `password` = \'bar\'',
        ], [
            static::sqlite(),
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            static::postgres(),
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            static::sqlServer(),
            'select * from [users] where [name] = \'foo\' and [password] = \'bar\'',
        ]];
    }

    /**
     * @dataProvider inProvider
     *
     * @param  Builder  $query
     * @param  string  $expected
     */
    public function test_it_should_get_condition_in_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper();

        $query->from('users')->whereIn('id', [0, new Expression(1), null, false, 'foo']);

        $this->assertSql($expected, $dumper, $query);
    }

    public static function inProvider(): array
    {
        return [[
            static::mysql(),
            'select * from `users` where `id` in (0, 1, NULL, 0, \'foo\')',
        ], [
            static::sqlite(),
            'select * from "users" where "id" in (0, 1, NULL, 0, \'foo\')',
        ], [
            static::postgres(),
            'select * from "users" where "id" in (0, 1, NULL, 0, \'foo\')',
        ], [
            static::sqlServer(),
            'select * from [users] where [id] in (0, 1, NULL, 0, \'foo\')',
        ]];
    }

    /**
     * @dataProvider withoutQuoteGrammarProvider
     *
     * @param  Builder  $query
     * @param  string  $expected
     */
    public function test_it_should_convert_to_without_quote_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::WITHOUT_QUOTE);

        $query->from('users')->whereIn('id', [1, null, false, 'foo']);

        $this->assertSql($expected, $dumper, $query);
    }

    public static function withoutQuoteGrammarProvider(): array
    {
        return [[
            static::mysql(),
            'select * from users where id in (1, NULL, 0, \'foo\')',
        ], [
            static::sqlite(),
            'select * from users where id in (1, NULL, 0, \'foo\')',
        ], [
            static::postgres(),
            'select * from users where id in (1, NULL, 0, \'foo\')',
        ], [
            static::sqlServer(),
            'select * from users where id in (1, NULL, 0, \'foo\')',
        ]];
    }

    /**
     * @dataProvider mySQLGrammarProvider
     *
     * @param  Builder  $query
     * @param  string  $expected
     */
    public function test_it_should_convert_to_mysql_version_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::MYSQL);

        $query->from('users')->whereIn('id', [1, null, false, 'foo', 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public static function mySQLGrammarProvider(): array
    {
        return [[
            static::mysql(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\\\'t be late\')',
        ], [
            static::sqlite(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\\\'t be late\')',
        ], [
            static::postgres(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\\\'t be late\')',
        ], [
            static::sqlServer(),
            'select * from `users` where `id` in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\\\'t be late\')',
        ]];
    }

    /**
     * @dataProvider sqliteGrammarProvider
     *
     * @param  Builder  $query
     * @param  string  $expected
     */
    public function test_it_should_convert_to_sqlite_version_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::SQLITE);

        $query->from('users')->whereIn('id', [1, null, false, 'foo', 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public static function sqliteGrammarProvider(): array
    {
        return [[
            static::mysql(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\User\', \'don\'\'t be late\')',
        ], [
            static::sqlite(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\User\', \'don\'\'t be late\')',
        ], [
            static::postgres(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\User\', \'don\'\'t be late\')',
        ], [
            static::sqlServer(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\User\', \'don\'\'t be late\')',
        ]];
    }

    /**
     * @dataProvider postgresGrammarProvider
     *
     * @param  Builder  $query
     * @param  string  $expected
     */
    public function test_it_should_convert_to_postgres_version_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::POSTGRES);

        $query->from('users')->whereIn('id', [1, null, false, 'foo', 'App\User', 'don\'t be late']);

        $this->assertSql($expected, $dumper, $query);
    }

    public static function postgresGrammarProvider(): array
    {
        return [[
            static::mysql(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\'\'t be late\')',
        ], [
            static::sqlite(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\'\'t be late\')',
        ], [
            static::postgres(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\'\'t be late\')',
        ], [
            static::sqlServer(),
            'select * from "users" where "id" in (1, NULL, 0, \'foo\', \'App\\\\User\', \'don\'\'t be late\')',
        ]];
    }

    /**
     * @dataProvider sqlServerGrammarProvider
     *
     * @param  Builder  $query
     * @param  string  $expected
     */
    public function test_it_should_convert_to_sqlserver_version_sql(Builder $query, string $expected): void
    {
        $dumper = $this->givenDumper(Dumper::SQLSERVER);

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertSql($expected, $dumper, $query);
    }

    public static function sqlServerGrammarProvider(): array
    {
        return [[
            static::mysql(),
            'select * from [users] where [id] in (1, 2, 3, 4, 5)',
        ], [
            static::sqlite(),
            'select * from [users] where [id] in (1, 2, 3, 4, 5)',
        ], [
            static::postgres(),
            'select * from [users] where [id] in (1, 2, 3, 4, 5)',
        ], [
            static::sqlServer(),
            'select * from [users] where [id] in (1, 2, 3, 4, 5)',
        ]];
    }

    /**
     * @param  string  $expected
     * @param  Dumper  $dumper
     * @param  Builder  $query
     */
    private function assertSql(string $expected, Dumper $dumper, Builder $query): void
    {
        self::assertEquals(
            $expected,
            $dumper->dump($query->toSql(), $query->getBindings(), false),
            $this->getDriver($query)
        );
    }

    private function getDriverFromGrammar(Grammar $grammar)
    {
        preg_match('/(?<driver>\w+)Grammar/', get_class($grammar), $matched);

        return strtolower($matched['driver']);
    }
}
