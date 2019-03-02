<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

use function array_combine;
use function array_map;
use InvalidArgumentException;
use Stratadox\Pathfinder\Network;
use Stratadox\Pathfinder\Labels;

final class Graph implements Network
{
    /** @var Vertex[] */
    private $vertices;
    private $ids;

    private function __construct(Vertex ...$vertices)
    {
        /** @var Vertex[] */
        $verticesByLabel = array_combine(
            array_map(function (Vertex $vertex): string {
                return $vertex->label();
            }, $vertices),
            $vertices
        );
        $this->vertices = $verticesByLabel;
        $this->ids = Ids::for(...$vertices);
    }

    public static function with(Vertex ...$vertices): Network
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
