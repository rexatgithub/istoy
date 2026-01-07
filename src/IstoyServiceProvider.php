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

        $this->publishes([
            __DIR__ . '/../database/migrations/2024_01_01_000000_add_istoy_columns_to_orders_table.php' => database_path('migrations/' . date('Y_m_d_His') . '_add_istoy_columns_to_orders_table.php'),
        ], 'istoy-migrations');
    }
}

