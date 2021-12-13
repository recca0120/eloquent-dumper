<?php

namespace Recca0120\EloquentDumper;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Recca0120\EloquentDumper\Output\ConsoleOutput;
use Recca0120\EloquentDumper\Output\OutputInterface;
use Recca0120\EloquentDumper\Output\SymfonyDumpOutput;

class EloquentDumperServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/eloquent-dumper.php', 'eloquent-dumper');

        $this->app->bind(Dumper::class, function () {
            return Dumper::factory($this->getConfig('grammar'));
        });

        $this->app->singleton(OutputInterface::class, function () {
            return $this->app->runningInConsole() ? new ConsoleOutput() : new SymfonyDumpOutput();
        });

        $this->registerBuilderMicro('toRawSql', function () {
            return app(Dumper::class)
                ->setPdo($this->getConnection()->getPdo())
                ->dump($this->toSql(), $this->getBindings());
        });

        $this->registerBuilderMicro('dumpSql', function () {
            app(OutputInterface::class)->output($this->toRawSql());

            return $this;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/eloquent-dumper.php' => config_path('eloquent-dumper.php'),
            ], 'eloquent-dumper');
        }

        $this->app['config']->set(
            'logging.channels.eloquent-dumper',
            $this->getConfig('logging.channel')
        );

        DB::listen(function (QueryExecuted $event) {
            $request = app(Request::class);
            $sql = app(Dumper::class)
                ->setPdo($event->connection->getPdo())
                ->dump($event->sql, $event->bindings);

            Log::channel('eloquent-dumper')->debug(
                strtr($this->getConfig('logging.format'), [
                    '%connection-name%' => $event->connectionName,
                    '%time%' => static::formatDuration($event->time),
                    '%sql%' => $sql,
                    '%method%' => $request->method(),
                    '%uri%' => $request->getRequestUri(),
                ])
            );
        });
    }

    private function registerBuilderMicro($method, Closure $closure): void
    {
        Builder::macro($method, $closure);
        EloquentBuilder::macro($method, $closure);
    }

    public static function formatDuration(float $time): string
    {
        if ($time < 0.001) {
            return round($time * 1000000).'μs';
        }

        return $time < 1
            ? round($time * 1000, 2).'ms'
            : round($time, 2).'s';
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getConfig(string $name)
    {
        return $this->app['config']['eloquent-dumper.'.$name];
    }
}
