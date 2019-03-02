<?php

namespace Stratadox\Pathfinder\Graph;

interface Edge
{
    public function target(): string;
    public function cost(): float;
}
