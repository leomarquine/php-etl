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
            __DIR__ . '/config.php' => config_path('etl.php'),
        ]);

        foreach (config('database.connections') as $name => $connection) {
            if (in_array($connection['driver'], ['sqlite', 'mysql'])) {
                if ($name == config('database.default')) {
                    Etl::addConnection($connection, 'default');
                }

                Etl::addConnection($connection, $name);
            }
        }

        if (config('etl')) {
            Etl::config(config('etl'));
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
