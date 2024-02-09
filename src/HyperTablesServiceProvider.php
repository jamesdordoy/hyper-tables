<?php

namespace JamesDordoy\HyperTables;

use JamesDordoy\HyperTables\Commands\HyperTablesMigrateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HyperTablesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('hyper-tables')
            ->hasConfigFile()
            ->hasCommand(HyperTablesMigrateCommand::class);
    }
}
