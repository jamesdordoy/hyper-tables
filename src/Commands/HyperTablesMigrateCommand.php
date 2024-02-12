<?php

namespace JamesDordoy\HyperTables\Commands;

use Illuminate\Console\Command;
use JamesDordoy\HyperTables\ModelFinder;

class HyperTablesMigrateCommand extends Command
{
    public $signature = 'hyper-tables-migrate';

    public $description = 'Run any migrations across your HyperTables';

    public function handle(): int
    {
        // in this class locally cache the run migration messages and then display them if there are migrations running..
        ModelFinder::all(config('hyper-tables.table_path'))
            ->map(fn($namespace) => new $namespace)
            ->map(fn($table) => $this->comment(sprintf('Run migration on table: %s', $table->getModel()->getTable())));

        return self::SUCCESS;
    }
}
