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
            $grammar = Arr::get($app['config'], 'eloquent-dumper.grammar', Dumper::PDO);

            return new Dumper($grammar);
        });

        $this->registerBuilderMicro('toRawSql', function () {
            return app(EloquentHelper::class)->toRawSql($this);
        });

        $runningInConsole = $this->app->runningInConsole();
        $this->registerBuilderMicro('dumpSql', function () use ($runningInConsole) {
            return app(EloquentHelper::class)->dumpSql(
                $this, $runningInConsole
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
    }

    private function registerBuilderMicro($method, Closure $closure)
    {
        Builder::macro($method, $closure);
        EloquentBuilder::macro($method, $closure);
    }
}
