<?php

namespace JamesDordoy\HyperTables\Facades;

use Illuminate\Support\Facades\Facade;

class HyperTables extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \JamesDordoy\HyperTables\HyperTables::class;
    }
}
