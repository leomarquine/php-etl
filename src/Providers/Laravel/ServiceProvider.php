<?php

namespace Marquine\Metis\Providers\Laravel;

use Marquine\Metis\Metis;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('metis.php'),
        ]);

        foreach (config('database.connections') as $name => $connection) {
            if ($name == config('database.default')) {
                Metis::addConnection($connection, 'default');
            }
            Metis::addConnection($connection, $name);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('metis', function () {
            return Metis::config(config('metis'));
        });
    }
}
