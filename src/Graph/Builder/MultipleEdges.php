<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph\Builder;

use function array_merge;
use function array_reduce;
use Stratadox\Pathfinder\Graph\Edges;
use Stratadox\Pathfinder\Graph\Roads;

final class MultipleEdges implements EdgeDefinition
{
    private $edges;

    private function __construct(EdgeDefinition ...$edges)
    {
        $this->edges = $edges;
    }

    public static function consistingOf(EdgeDefinition ...$edges): EdgeDefinition
    {
        return new self(...$edges);
    }

    public function andTo(string $target, float $cost = 1.0): EdgeDefinition
    {
        return new self(
            ...array_merge($this->edges, [WithEdge::to($target, $cost)])
        );
    }

    public function gather(): Edges
    {
        return array_reduce(
            $this->edges,
            function (Edges $carry, EdgeDefinition $edge): Edges {
                return $carry->merge($edge->gather());
            },
            Roads::none()
        );
    }
}
