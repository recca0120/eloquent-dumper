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
            $config = Arr::get($app['config'], 'eloquent-dumper', [
                'driver' => Dumper::DEFAULT,
            ]);

            return new Dumper($config['driver']);
        });

        $sql = function (QueryBuilder $query) {
            return app(Dumper::class)->sql($query);
        };

        $dumpSql = function (QueryBuilder $query) use ($sql) {
            $sql = $sql($query);
            if (app()->runningInConsole()) {
                echo "\n" . $sql . "\n";
            } else {
                dump($sql);
            }

            return $this;
        };

        Builder::macro('sql', function () use ($sql) {
            return $sql($this->query);
        });
        QueryBuilder::macro('sql', function () use ($sql) {
            return $sql($this);
        });

        Builder::macro('dumpSql', function () use ($dumpSql) {
            return $dumpSql($this->query);
        });
        QueryBuilder::macro('dumpSql', function () use ($dumpSql) {
            return $dumpSql($this);
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
                __DIR__ . '/../config/eloquent-dumper.php' => config_path('eloquent-dumper.php'),
            ], 'eloquent-dumper');
        }
    }
}
