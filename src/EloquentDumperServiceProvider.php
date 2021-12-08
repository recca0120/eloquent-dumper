<?php

namespace Recca0120\EloquentDumper;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Query\Builder;
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

        $config = $this->app['config'];
        $config->set('logging.channels.eloquent-dumper', $config->get('eloquent-dumper.logging.channel'));

        $this->app->bind(Dumper::class, function () use ($config) {
            return new Dumper($config->get('eloquent-dumper.grammar'));
        });

        $this->app->singleton(OutputInterface::class, function () {
            return $this->app->runningInConsole() ? new ConsoleOutput() : new SymfonyDumpOutput();
        });

        $this->registerBuilderMicro('toRawSql', function () {
            /** @var Builder $query */
            $query = $this;

            return app(Dumper::class)
                ->setPdo($query->getConnection()->getPdo())
                ->dump($query->toSql(), $query->getBindings());
        });

        $this->registerBuilderMicro('dumpSql', function () {
            /** @var Builder $query */
            $query = $this;

            return app(OutputInterface::class)->dump(
                app(Dumper::class)
                    ->setPdo($query->getConnection()->getPdo())
                    ->dump($query->toSql(), $query->getBindings(), true)
            );
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

        DB::listen(function (QueryExecuted $event) {
            $connectionName = $event->connectionName;
            $time = static::formatDuration($event->time);
            $sql = app(Dumper::class)
                ->setPdo($event->connection->getPdo())
                ->dump($event->sql, $event->bindings);

            Log::channel('eloquent-dumper')->debug(
                vsprintf('[%s] [%s] %s', [$connectionName, $time, $sql])
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
}
