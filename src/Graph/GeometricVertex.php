<?php

namespace Stratadox\Pathfinder\Graph;

use Stratadox\Pathfinder\Position;

interface GeometricVertex extends Vertex
{
    public function position(): Position;
}
