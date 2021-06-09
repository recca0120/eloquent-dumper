<?php

namespace Recca0120\EloquentDumper;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
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

        $this->registerBuilderMicro('toRawSql', function () {
            return app(EloquentHelper::class)->toRawSql($this);
        });

        $this->registerBuilderMicro('dumpSql', function () {
            return app(EloquentHelper::class)->dumpSql($this);
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
    }

    private function registerBuilderMicro(string $method, Closure $closure)
    {
        Builder::macro($method, $closure);
        EloquentBuilder::macro($method, $closure);
    }
}
