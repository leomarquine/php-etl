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
        $connections = $this->app['config']['database.connections'];
        $connections['default'] = $connections[$this->app['config']['database.default']];

        foreach ($connections as $name => $config) {
            Etl::service('db')->addConnection($config, $name);
        }

        $this->app->bind(Etl::class, function ($app) {
            return new Etl;
        });
    }
}
