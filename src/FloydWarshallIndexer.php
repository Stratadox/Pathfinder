<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use Stratadox\Pathfinder\Estimate\Indexed;
use Stratadox\Pathfinder\Graph\GeometricView;

final class FloydWarshallIndexer implements Indexer
{
    private $graph;
    private $shortestPaths;
    private $distances;

    public function __construct(Network $network)
    {
        $this->graph = $network;
    }

    public static function operatingIn(Network $network): Indexer
    {
        return new self($network);
    }

    public function allShortestPaths(): ShortestPathForest
    {
        if (!$this->shortestPaths) {
            $this->calculateIndex();
        }
        return Index::of($this->shortestPaths);
    }

    public function heuristic(): Heuristic
    {
        if (!$this->distances) {
            $this->calculateIndex();
        }
        return Indexed::heuristic($this->distances, GeometricView::of($this->graph));
    }

    private function calculateIndex(): void
    {
        $dist = [];
        $next = [];
        foreach ($this->graph->all() as $node) {
            $dist[$node][$node] = 0;
            foreach ($this->graph->neighboursOf($node) as $neighbour) {
                $dist[$node][$neighbour] = $this->graph->movementCostBetween($node, $neighbour);
                $next[$node][$neighbour] = $neighbour;
            }
        }
        $all = $this->graph->all()->items();
        foreach ($all as $node) {
            foreach ($all as $start) {
                $begin = ($dist[$start][$node] ?? INF);
                foreach ($all as $goal) {
                    $end = ($dist[$node][$goal] ?? INF);
                    $proposition = $begin + $end;
                    $current = ($dist[$start][$goal] ?? INF);

                    if ($proposition < $current) {
                        $dist[$start][$goal] = $dist[$start][$node] + $dist[$node][$goal];
                        $next[$start][$goal] = $next[$start][$node] ?? null;
                    }
                }
            }
        }
        $this->distances = $dist;
        $this->shortestPaths = $next;
    }
}
