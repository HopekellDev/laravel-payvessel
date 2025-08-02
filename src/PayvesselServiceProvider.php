<?php

namespace HopekellDev\Payvessel;

use Illuminate\Support\ServiceProvider;

class PayvesselServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/payvessel.php', 'payvessel');

        $this->app->singleton('payvessel', function () {
            return new Payvessel(config('payvessel'));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/payvessel.php' => config_path('payvessel.php'),
        ], 'config');
    }
}
