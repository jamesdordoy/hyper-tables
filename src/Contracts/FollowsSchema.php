<?php

namespace JamesDordoy\HyperTables\Contracts;

interface FollowsSchema
{
    public function create(): void;

    public function table(): void;

    public function drop(): void;

    public function rename(string $from, string $to): void;
}
