<?php

namespace Stratadox\Pathfinder\Graph;

use Stratadox\Collection\Appendable;
use Stratadox\Collection\Mergeable;

interface Edges extends Appendable, Mergeable
{
    public function current(): Edge;
}
