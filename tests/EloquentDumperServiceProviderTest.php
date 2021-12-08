<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase;
use Recca0120\EloquentDumper\EloquentDumperServiceProvider;

class EloquentDumperServiceProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('database.default', 'testing');
    }

    /**
     * @dataProvider grammarProvider
     */
    public function test_eloquent_dump_sql(string $grammar, string $excepted, string $exceptedOutput): void
    {
        $this->app['config']->set('eloquent-dumper.grammar', $grammar);
        $query = StubUser::where('name', 'foo')->where('password', 'bar');

        self::assertEquals($excepted, $query->toRawSql());
        $this->assertOutput($exceptedOutput, $query);
    }

    public function grammarProvider(): array
    {
        return [
            ['mysql', 'select * from `users` where `name` = \'foo\' and `password` = \'bar\'', 'SELECT * FROM `users` WHERE `name` = \'foo\' AND `password` = \'bar\''],
            ['sqlite', 'select * from "users" where "name" = \'foo\' and "password" = \'bar\'', 'SELECT * FROM "users" WHERE "name" = \'foo\' AND "password" = \'bar\''],
            ['pgsql', 'select * from "users" where "name" = \'foo\' and "password" = \'bar\'', 'SELECT * FROM "users" WHERE "name" = \'foo\' AND "password" = \'bar\''],
            ['sqlsrv', 'select * from [users] where [name] = \'foo\' and [password] = \'bar\'', 'SELECT * FROM [ users ] WHERE [ NAME ] = \'foo\' AND [ PASSWORD ] = \'bar\''],
        ];
    }

    protected function getPackageProviders($app): array
    {
        return [EloquentDumperServiceProvider::class];
    }

    /**
     * @param string $expected
     * @param Builder $query
     */
    private function assertOutput(string $expected, Builder $query)
    {
        ob_start();
        $query->dumpSql();
        $content = ob_get_clean();
        $output = str_replace(["\x1b[35m", "\x1b[36m", "\x1b[39m", "\x1b[91m", "\x1b[92m", "\x1b[95m", "\x1b[0m"], '', $content);
        $output = preg_replace('/\r\n|\n/', ' ', $output);
        $output = trim(preg_replace('/\s+/', ' ', $output));

        self::assertEquals($expected, $output);
    }
}

class StubUser extends Model
{
    protected $table = 'users';
}
