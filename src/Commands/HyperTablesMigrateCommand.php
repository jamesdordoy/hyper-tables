<?php

namespace JamesDordoy\HyperTables\Commands;

use Illuminate\Console\Command;

class HyperTablesMigrateCommand extends Command
{
    public $signature = 'hyper-tables-migrate';

    public $description = 'Run any migrations across your HyperTables';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
