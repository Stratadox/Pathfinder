<?php

namespace Stratadox\Pathfinder\Graph;

interface Vertex
{
    public function label(): string;
    public function edges(): Edges;
    public function hasNegativeEdges(): bool;
}
