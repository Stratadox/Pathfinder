<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

use function array_combine;
use function array_map;
use InvalidArgumentException;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Labels;
use Stratadox\Pathfinder\Position;

final class GeometricGraph implements Environment
{
    /** @var GeometricVertex[] */
    private $vertices;
    private $ids;

    private function __construct(GeometricVertex ...$vertices)
    {
        $this->vertices = array_combine(
            array_map(function (Vertex $vertex): string {
                return $vertex->label();
            }, $vertices),
            $vertices
        );
        $this->ids = Ids::for(...$vertices);
    }

    public static function with(GeometricVertex ...$vertices): Environment
    {
        return new self(...$vertices);
    }

    public function all(): Labels
    {
        return $this->ids;
    }

    public function has(string $node): bool
    {
        return isset($this->vertices[$node]);
    }

    public function neighboursOf(string $source): Labels
    {
        $neighbours = [];
        foreach ($this->vertices[$source]->edges() as $edge) {
            $neighbours[] = $edge->target();
        }
        return Ids::consistingOf(...$neighbours);
    }

    public function areNeighbours(string $source, string $neighbour): bool
    {
        foreach ($this->vertices[$source]->edges() as $edge) {
            if ($neighbour === $edge->target()) {
                return true;
            }
        }
        return false;
    }

    public function movementCostBetween(string $source, string $neighbour): float
    {
        foreach ($this->vertices[$source]->edges() as $edge) {
            if ($neighbour === $edge->target()) {
                return $edge->cost();
            }
        }
        throw new InvalidArgumentException(
            "Vertices $source and $neighbour are not neighbours."
        );
    }

    public function positionOf(string $node): Position
    {
        return $this->vertices[$node]->position();
    }

    public function hasNegativeEdgeCosts(): bool
    {
        foreach ($this->vertices as $vertex) {
            if ($vertex->hasNegativeEdges()) {
                return true;
            }
        }
        return false;
    }
}
