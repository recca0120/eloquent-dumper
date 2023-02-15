<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase;
use org\bovigo\vfs\vfsStream;
use Recca0120\EloquentDumper\EloquentDumperServiceProvider;

class EloquentDumperServiceProviderTest extends TestCase
{
    private $sqlFile;

    /**
     * @dataProvider sqlProvider
     */
    public function test_dump_sql(string $grammar, string $excepted, string $exceptedOutput): void
    {
        $this->setGrammar($grammar);
        $query = User::where('name', 'foo')->where('password', 'bar');
        $sql = $query->toRawSql();
        $dumpSql = $this->dumpSql($query);

        self::assertEquals($excepted, $sql);
        self::assertEquals($excepted, $dumpSql);
    }

    /**
     * @dataProvider sqlProvider
     */
    public function test_log_sql(string $grammar, string $excepted): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->setGrammar($grammar);
        User::where('name', 'foo')->where('password', 'bar')->get();

        self::assertStringContainsString('testing '.$excepted.' | GET', $this->sqlFile->getContent());
    }

    public static function sqlProvider(): array
    {
        return [[
            'mysql',
            'select * from `users` where `name` = \'foo\' and `password` = \'bar\'',
            'select * from `users` where `name` = \'foo\' and `password` = \'bar\'',
        ], [
            'sqlite',
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            'pgsql',
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
        ], [
            'sqlsrv',
            'select * from [users] where [name] = \'foo\' and [password] = \'bar\'',
            'select * from [users] where [name] = \'foo\' and [password] = \'bar\'',
        ]];
    }

    protected function getEnvironmentSetUp($app)
    {
        $root = vfsStream::setup();
        $this->sqlFile = vfsStream::newFile('sql.log')->at($root);
        $this->slowSqlFile = vfsStream::newFile('slow-sql.log')->at($root);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('eloquent-dumper.logging.format', '%connection-name% %sql% | %method%');
        $app['config']->set('eloquent-dumper.logging.channels.log.path', $this->sqlFile->url());
        $app['config']->set('eloquent-dumper.logging.channels.slow-sql.path', $this->slowSqlFile->url());
    }

    protected function getPackageProviders($app): array
    {
        return [EloquentDumperServiceProvider::class];
    }

    /**
     * @param  string  $grammar
     * @return void
     */
    private function setGrammar(string $grammar): void
    {
        $this->app['config']->set('eloquent-dumper.grammar', $grammar);
    }

    /**
     * @param  Builder  $query
     * @return string
     */
    private function dumpSql(Builder $query): string
    {
        ob_start();
        $query->dumpSql();
        $output = ob_get_clean();
        $output = str_replace([
            "\x1b[35m",
            "\x1b[36m",
            "\x1b[39m",
            "\x1b[91m",
            "\x1b[92m",
            "\x1b[95m",
            "\x1b[0m",
            "\e[0m",
            "\e[34;1m",
            "\e[35;1m",
            "\e[37m",
        ], '', $output);
        $output = preg_replace('/\r\n|\n/', ' ', $output);

        return trim(preg_replace('/\s+/', ' ', $output));
    }
}

class User extends Model
{
}
