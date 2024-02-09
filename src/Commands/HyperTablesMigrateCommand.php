<?php

namespace JamesDordoy\HyperTables\Commands;

use Illuminate\Console\Command;

class HyperTablesMigrateCommand extends Command
{
    public $signature = 'hyper-tables-migrate';

    public $description = 'Run any migrations across your HyperTables';

    public function handle(): int
    {
        collect(config('hyper-tables.tables'))->each(
            fn (string $namespace) => tap(new $namespace, fn ($table) => $this->comment(sprintf('Run migration on table: %s', $table->getModel()->getTable())
            )
            )
        );

        return self::SUCCESS;
    }
}
