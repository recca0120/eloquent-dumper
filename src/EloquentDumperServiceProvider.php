<?php

namespace Recca0120\EloquentDumper;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class EloquentDumperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Dumper::class, Dumper::class);

        Builder::macro('rawSql', function ($wrap = true) {
            return app(Dumper::class)->rawSql($this, $wrap);
        });

        Builder::macro('dumpRawSql', function ($wrap = true) {
            $runningInConsole = app()->runningInConsole();
            $sql = app(Dumper::class)->rawSql($this, $wrap);

            if (! $runningInConsole && method_exists('dump')) {
                dump($sql);
            } else {
                echo "\n".$sql."\n";
            }

            return $this;
        });
    }
}
