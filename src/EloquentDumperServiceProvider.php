<?php

namespace Recca0120\EloquentDumper;

use Closure;
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
                Arr::get($app['config'], 'eloquent-dumper.grammar', Dumper::PDO)
            );
        });

        $this->app->singleton(EloquentHelper::class, EloquentHelper::class);

        Builder::macro('sql', self::sql());
        QueryBuilder::macro('sql', self::sql());
        Builder::macro('dumpSql', self::dump());
        QueryBuilder::macro('dumpSql', self::dump());
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
     * @return Closure
     */
    private static function sql()
    {
        return function () {
            return app(EloquentHelper::class)->sql($this);
        };
    }

    /**
     * @return Closure
     */
    private static function dump()
    {
        return function () {
            return app(EloquentHelper::class)->dump($this);
        };
    }
}
