<?php

namespace JamesDordoy\HyperTables\Attributes;

use JamesDordoy\HyperTables\Models\Table;
use Attribute;
use Exception;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Migrate
{
   public function __construct(
        public string $class
   ) {
        if (! is_subclass_of($this->class, Table::class)) {
            throw new Exception('blahh');
        }
   }
}
