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

    public function handle(): int
    {
        // in this class locally cache the run migration messages and then display them if there are migrations running..
        $models = ModelFinder::all(config('hyper-tables.table_path'))
            ->map(function (string $namespace) {
                try {
                    return new $namespace;
                } catch (Exception|Error) {
                    return null;
                }
            })
            ->map(fn (Table $table) => $this->comment(sprintf('Run migration on table: %s', $table->getModel()->getTable())));

        return self::SUCCESS;
    }
}
