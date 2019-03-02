<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Network;
use Stratadox\Pathfinder\Labels;
use Stratadox\Pathfinder\Position;

final class GeometricView implements Environment
{
    private $actualGraph;

    private function __construct(Network $actualGraph)
    {
        $this->actualGraph = $actualGraph;
    }

    public static function of(Network $actualGraph): Environment
    {
        if ($actualGraph instanceof Environment) {
            return $actualGraph;
        }
        return new self($actualGraph);
    }

    public function all(): Labels
    {
        return $this->actualGraph->all();
    }

    public function positionOf(string $node): Position
    {
        return At::position();
    }

    public function neighboursOf(string $node): Labels
    {
        return $this->actualGraph->neighboursOf($node);
    }

    public function areNeighbours(string $source, string $neighbour): bool
    {
        return $this->actualGraph->areNeighbours($source, $neighbour);
    }

    public function has(string $node): bool
    {
        return $this->actualGraph->has($node);
    }

    public function movementCostBetween(string $source, string $neighbour): float
    {
        return $this->actualGraph->movementCostBetween($source, $neighbour);
    }

    public function hasNegativeEdgeCosts(): bool
    {
        return $this->actualGraph->hasNegativeEdgeCosts();
    }
}
