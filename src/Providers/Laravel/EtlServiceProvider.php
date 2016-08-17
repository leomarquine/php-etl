<?php

namespace Marquine\Etl\Providers\Laravel;

use Marquine\Etl\Etl;
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
        $this->publishes([
            __DIR__ . '/etl.php' => config_path('etl.php'),
        ]);

        Etl::config(config('etl'));
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
