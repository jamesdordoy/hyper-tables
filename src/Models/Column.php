<?php

namespace JamesDordoy\HyperTables\Models;

class Column
{
    public function __construct(
        protected string $name,
        protected string $type,
    ) {

    }
}
