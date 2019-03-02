<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph\Builder;

use Stratadox\Pathfinder\Graph\Edges;
use Stratadox\Pathfinder\Graph\Roads;

final class WithoutEdges implements EdgeDefinition
{
    public static function poorThing(): EdgeDefinition
    {
        return new self();
    }

    public function andTo(string $target, float $cost = 1.0): EdgeDefinition
    {
        return WithEdge::to($target, $cost);
    }

    public function gather(): Edges
    {
        return Roads::none();
    }
}
