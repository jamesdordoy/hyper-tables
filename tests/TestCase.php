<?php

namespace JamesDordoy\HyperTables\Tests;

use JamesDordoy\HyperTables\HyperTablesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            HyperTablesServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_hyper-tables_table.php.stub';
        $migration->up();
        */
    }
}
