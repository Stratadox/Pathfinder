<?php

namespace Stratadox\Pathfinder\Graph\Builder;

use Stratadox\Pathfinder\Graph\Edges;

interface EdgeDefinition
{
    public function andTo(string $target, float $cost = 1.0): EdgeDefinition;
    public function gather(): Edges;
}
