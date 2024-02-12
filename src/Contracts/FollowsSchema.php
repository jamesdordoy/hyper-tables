<?php

namespace JamesDordoy\HyperTables\Contracts;

interface FollowsSchema
{
    public function create();

    public function table();

    public function drop(): void;

    public function rename(string $from, string $to);
}
