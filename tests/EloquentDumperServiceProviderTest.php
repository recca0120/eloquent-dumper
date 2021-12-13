<?php

namespace Recca0120\EloquentDumper\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase;
use org\bovigo\vfs\vfsStream;
use Recca0120\EloquentDumper\EloquentDumperServiceProvider;

class EloquentDumperServiceProviderTest extends TestCase
{
    private $file;

    protected function getEnvironmentSetUp($app)
    {
        $root = vfsStream::setup();
        $this->file = vfsStream::newFile('sql.log')->at($root);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('eloquent-dumper.logging.channel.path', $this->file->url());
        $app['config']->set('eloquent-dumper.logging.format', '%connection-name% %sql% | %method%');
    }

    /**
     * @dataProvider sqlProvider
     */
    public function test_dump_sql(string $grammar, string $excepted, string $exceptedOutput): void
    {
        $this->setGrammar($grammar);
        $query = User::where('name', 'foo')->where('password', 'bar');

        self::assertEquals($excepted, $query->toRawSql());
        $this->assertOutput($exceptedOutput, $query);
    }

    /**
     * @dataProvider sqlProvider
     */
    public function test_log_sql(string $grammar, string $excepted): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->setGrammar($grammar);
        User::where('name', 'foo')->where('password', 'bar')->get();

        self::assertStringContainsString(
            'testing '.$excepted.' | GET',
            $this->file->getContent()
        );
    }

    public function sqlProvider(): array
    {
        return [[
            'mysql',
            'select * from `users` where `name` = \'foo\' and `password` = \'bar\'',
            'SELECT * FROM `users` WHERE `name` = \'foo\' AND `password` = \'bar\'',
        ], [
            'sqlite',
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
            'SELECT * FROM "users" WHERE "name" = \'foo\' AND "password" = \'bar\'',
        ], [
            'pgsql',
            'select * from "users" where "name" = \'foo\' and "password" = \'bar\'',
            'SELECT * FROM "users" WHERE "name" = \'foo\' AND "password" = \'bar\'',
        ], [
            'sqlsrv',
            'select * from [users] where [name] = \'foo\' and [password] = \'bar\'',
            'SELECT * FROM [ users ] WHERE [ NAME ] = \'foo\' AND [ PASSWORD ] = \'bar\'',
        ]];
    }

    protected function getPackageProviders($app): array
    {
        return [EloquentDumperServiceProvider::class];
    }

    /**
     * @param string $expected
     * @param Builder $query
     */
    private function assertOutput(string $expected, Builder $query): void
    {
        ob_start();
        $query->dumpSql();
        $content = ob_get_clean();
        $output = str_replace(["\x1b[35m", "\x1b[36m", "\x1b[39m", "\x1b[91m", "\x1b[92m", "\x1b[95m", "\x1b[0m"], '', $content);
        $output = preg_replace('/\r\n|\n/', ' ', $output);
        $output = trim(preg_replace('/\s+/', ' ', $output));

        self::assertEquals($expected, $output);
    }

    /**
     * @param string $grammar
     * @return void
     */
    private function setGrammar(string $grammar): void
    {
        $this->app['config']->set('eloquent-dumper.grammar', $grammar);
    }
}

class User extends Model
{
}
