<?php

namespace Istoy;

use Illuminate\Support\ServiceProvider;

class IstoyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/istoy.php',
            'istoy'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/istoy.php' => config_path('istoy.php'),
        ], 'istoy-config');
    }
}

