<?php

namespace JamesDordoy\HyperTables\Attributes;

use Attribute;
use JamesDordoy\HyperTables\Exceptions\ModelNotHyperTableException;
use JamesDordoy\HyperTables\Models\Table;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Migrate
{
    public function __construct(
        public string $class
    ) {
        if (! is_subclass_of($this->class, Table::class)) {
            throw new ModelNotHyperTableException($this->class);
        }
    }
}
