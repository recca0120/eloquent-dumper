<?php

namespace Recca0120\EloquentDumper;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

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

        $this->app->singleton(Dumper::class, function ($app) {
            return new Dumper(
                Arr::get($app['config'], 'eloquent-dumper.driver', Dumper::DEFAULT)
            );
        });

        $closures = $this->getClosures();

        Builder::macro($closures['sql']['method'], $closures['sql']['fn']);
        QueryBuilder::macro($closures['sql']['method'], $closures['sql']['fn']);
        Builder::macro($closures['dump']['method'], $closures['dump']['fn']);
        QueryBuilder::macro($closures['dump']['method'], $closures['dump']['fn']);
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
    }

    /**
     * @return array[]
     */
    private function getClosures()
    {
        $app = $this->app;

        return [
            'sql' => ['method' => 'sql', 'fn' => function () use ($app) {
                return $app->make(Dumper::class)->dump($this->toSql(), $this->getBindings());
            }],
            'dump' => ['method' => 'dumpSql', 'fn' => function () use ($app) {
                $sql = $app->make(Dumper::class)->dump($this->toSql(), $this->getBindings());

                if ($app->runningInConsole()) {
                    echo "\n".$sql."\n";

                    return;
                }

                function_exists('dump') ? dump($sql) : var_dump($sql);
            }],
        ];
    }
}
