<?php

namespace Stratadox\Pathfinder\Graph\Builder;

interface Field
{
    public function isBlocked(): bool;
    public function label(): string;
    public function price(): float;
    public function costing(float $price): Field;
}
