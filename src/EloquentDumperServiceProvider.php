<?php

namespace Recca0120\EloquentDumper;

use Closure;
use Doctrine\SqlFormatter\NullHighlighter;
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
            return $this->app->runningInConsole()
                ? new ConsoleOutput()
                : new SymfonyDumpOutput();
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

        $this->registerBuilderMicro('toRawSql', function () {
            $pdo = $this->getConnection()->getPdo();
            $sql = $this->toSql();
            $bindings = $this->getBindings();

            return app(Dumper::class)->setPdo($pdo)->dump($sql, $bindings);
        });

        $this->registerBuilderMicro('dumpSql', function () {
            app(OutputInterface::class)->output($this->toRawSql());

            return $this;
        });

        $channels = $this->getConfig('logging.channels');
        foreach ($channels as $name => $channel) {
            $name = 'logging.channels.eloquent-dumper-'.$name;
            $this->app['config']->set($name, $channel);
        }

        $format = $this->getConfig('logging.format');
        $pattern = $this->getConfig('logging.pattern');
        $slowQueryExecTime = $this->getConfig('slow_query_exec_time');

        DB::listen(static function (QueryExecuted $event) use ($format, $pattern, $slowQueryExecTime) {
            $request = app(Request::class);
            $sql = app(Dumper::class)
                ->setPdo($event->connection->getPdo())
                ->dump($event->sql, $event->bindings);

            if (! preg_match($pattern, $sql)) {
                return;
            }

            $attributes = [
                '%connection-name%' => $event->connectionName,
                '%time%' => static::formatDuration($event->time),
                '%sql%' => $sql,
                '%formatted_sql%' => Dumper::format($sql, new NullHighlighter()),
                '%method%' => $request->method(),
                '%uri%' => $request->getRequestUri(),
            ];

            Log::channel('eloquent-dumper-log')
                ->debug(strtr($format, $attributes));

            if ($event->time > $slowQueryExecTime) {
                Log::channel('eloquent-dumper-slow-log')
                    ->debug(strtr($format, $attributes));
            }
        });
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

    private function registerBuilderMicro($method, Closure $closure): void
    {
        Builder::macro($method, $closure);
        EloquentBuilder::macro($method, $closure);
    }

    /**
     * @param  string  $name
     * @return mixed
     */
    private function getConfig(string $name)
    {
        return $this->app['config']['eloquent-dumper.'.$name];
    }
}
