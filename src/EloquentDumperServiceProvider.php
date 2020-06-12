<?php

namespace Recca0120\EloquentDumper;

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
        $this->app->singleton(Dumper::class, function ($app) {
            $config = Arr::get($app['config'], 'eloquent-dumper', [
                'driver' => Dumper::DEFAULT,
            ]);

            return new Dumper($config['driver']);
        });

        Builder::macro('sql', function () {
            return app(Dumper::class)->sql($this);
        });

        Builder::macro('dumpSql', function () {
            $sql = app(Dumper::class)->sql($this);
            if (app()->runningInConsole()) {
                echo "\n".$sql."\n";
            } else {
                dump($sql);
            }

            return $this;
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/eloquent-dumper.php' => config_path('eloquent-dumper.php'),
            ]);
        }
    }
}
