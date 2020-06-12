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
        $this->app->singleton(Dumper::class, function ($app) {
            return new Dumper(
                Arr::get($app['config'], 'eloquent-dumper.driver', Dumper::DEFAULT)
            );
        });

        $closures = $this->getClosures();

        Builder::macro($closures['sql']['name'], $closures['sql']['method']);
        QueryBuilder::macro($closures['sql']['name'], $closures['sql']['method']);
        Builder::macro($closures['dump']['name'], $closures['dump']['method']);
        QueryBuilder::macro($closures['dump']['name'], $closures['dump']['method']);
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
            'sql' => [
                'name' => 'sql',
                'method' => function () use ($app) {
                    return $app->make(Dumper::class)->dump($this->toSql(), $this->getBindings());
                },
            ],
            'dump' => [
                'name' => 'dumpSql',
                'method' => function () use ($app) {
                    $sql = $app->make(Dumper::class)->dump($this->toSql(), $this->getBindings());

                    if ($app->runningInConsole()) {
                        echo "\n".$sql."\n";

                        return;
                    }

                    function_exists('dump') ? dump($sql) : var_dump($sql);
                },
            ],
        ];
    }
}
