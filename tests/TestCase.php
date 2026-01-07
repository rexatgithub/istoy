<?php

namespace Istoy\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Istoy\IstoyServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Load package configuration
        $this->app['config']->set('istoy.order_model', \Istoy\Tests\Fixtures\Order::class);
        $this->app['config']->set('istoy.providers.smm.host', 'https://smmlite.com/api/v2');
        $this->app['config']->set('istoy.providers.smm.key', 'test_api_key');
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            IstoyServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}

