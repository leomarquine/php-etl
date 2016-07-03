<?php

namespace Marquine\Metis\Providers\Laravel;

use Marquine\Metis\Metis;
use Illuminate\Support\ServiceProvider;

class MetisServiceProvider extends ServiceProvider
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

            $connection = $this->normalizeConnection($connection);

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
        //
    }

    /**
     * Normalize Laravel connection params to connect using Doctrine DBAL.
     *
     * @param  array $connection
     * @return array
     */
    protected function normalizeConnection($connection)
    {
        switch ($connection['driver']) {
            case 'mysql':
                $connection['driver'] = 'pdo_mysql'; break;
            case 'sqlite':
                $connection['driver'] = 'pdo_sqlite'; break;
            case 'pgsql':
                $connection['driver'] = 'pdo_pgsql'; break;
        }

        if ($connection['driver'] != 'pdo_sqlite') {
            $connection['dbname'] = $connection['database'];
        } else {
            if ($connection['database'] == ':memory:') {
                $connection['memory'] = true;
            } else {
                $connection['path'] = $connection['database'];
            }
        }

        if (isset($connection['username'])) {
            $connection['user'] = $connection['username'];
        }

        return $connection;
    }
}
