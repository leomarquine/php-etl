<?php

namespace Marquine\Etl;

use Illuminate\Support\ServiceProvider;

class EtlServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Etl::config(config('etl'));

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/laravel.php' => config_path('etl.php'),
            ], 'etl');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
