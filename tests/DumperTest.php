<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\Query\Builder;

class DumperTest extends TestCase
{
    /**
     * @dataProvider simpleProvider
     * @param Builder $query
     * @param $expected
     */
    public function test_simple(Builder $query, $expected)
    {
        $dumper = $this->givenDumper();

        $query->from('users')
            ->where('name', 'foo')
            ->where('password', 'bar');

        $this->assertEquals($expected, $dumper->rawSql($query), $this->getDriver($query));
    }

    public function simpleProvider()
    {
        return [
            [$this->mysql(), "select * from `users` where `name` = 'foo' and `password` = 'bar'"],
            [$this->sqlite(), "select * from \"users\" where \"name\" = 'foo' and \"password\" = 'bar'"],
        ];
    }

    /**
     * @dataProvider inProvider
     * @param Builder $query
     * @param $expected
     */
    public function test_in(Builder $query, $expected)
    {
        $dumper = $this->givenDumper();

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertEquals($expected, $dumper->rawSql($query), $this->getDriver($query));
    }

    public function inProvider()
    {
        return [
            [$this->mysql(), 'select * from `users` where `id` in (1, 2, 3, 4, 5)'],
            [$this->sqlite(), 'select * from "users" where "id" in (1, 2, 3, 4, 5)'],
        ];
    }

    /**
     * @dataProvider inProvider
     * @param Builder $query
     */
    public function test_remove_wrap(Builder $query)
    {
        $dumper = $this->givenDumper();

        $query->from('users')->whereIn('id', [1, 2, 3, 4, 5]);

        $this->assertEquals(
            'select * from users where id in (1, 2, 3, 4, 5)',
            $dumper->rawSql($query, false),
            $this->getDriver($query)
        );
    }
}
