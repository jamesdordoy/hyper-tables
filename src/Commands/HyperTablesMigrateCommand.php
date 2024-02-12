<?php

namespace JamesDordoy\HyperTables\Commands;

use Error;
use Exception;
use Illuminate\Console\Command;
use JamesDordoy\HyperTables\ModelFinder;
use JamesDordoy\HyperTables\Models\Table;

class HyperTablesMigrateCommand extends Command
{
    public $signature = 'hyper-tables-migrate';

    public $description = 'Run any migrations across your HyperTables';

    protected bool $migrationsRun = false;

    public function handle(): int
    {
        ModelFinder::all(config('hyper-tables.table_path'))
            ->each(function (string $namespace) {
                $outstandingMigrations = $namespace::getOutstandingMigrations();

                if (!$outstandingMigrations->isEmpty()) {
                    $outstandingMigrations->each(fn($migration) => $this->comment(sprintf("Running migration: %s", $migration)));
                    $this->migrationsRun = true;
                }
            })
            ->map(function (string $namespace) {
                try {
                    return new $namespace;
                } catch (Exception|Error) {
                    return null;
                }
            });

        if (! $this->migrationsRun) {
            $this->info('Nothing to migrate.');
        }
        
        return self::SUCCESS;
    }
}
