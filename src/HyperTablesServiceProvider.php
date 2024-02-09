<?php

namespace JamesDordoy\HyperTables;

use JamesDordoy\HyperTables\Commands\HyperTablesMigrateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HyperTablesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('hyper-tables')
            ->hasConfigFile()
            ->hasCommand(HyperTablesMigrateCommand::class);
    }
}
