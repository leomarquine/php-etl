<?php

namespace Marquine\Etl;

use Illuminate\Support\ServiceProvider;

class EtlServiceProvider extends ServiceProvider
{
    /**
     * List of the supported database connections.
     *
     * @var array
     */
    protected $supportedConnections = [
        'mysql', 'pgsql', 'sqlite',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Etl::set('path', storage_path('app'));

        $this->addConnections();
    }

    /**
     * Add the connections to the ETL configuration.
     *
     * @return void
     */
    protected function addConnections()
    {
        foreach ($this->getSupportedConnections() as $name => $config) {
            Etl::addConnection($config, $name == config('database.default') ? 'default' : $name);
        }
    }

    /**
     * Get the supported connections configuration.
     *
     * @return array
     */
    protected function getSupportedConnections()
    {
        return array_intersect_key(
            config('database.connections'),
            array_flip($this->supportedConnections)
        );
    }
}
