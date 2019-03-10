<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use function array_reverse;
use const INF;
use SplPriorityQueue;
use Stratadox\Pathfinder\Reconstruction\PathRetracer;

final class MultiDijkstraPathfinder implements MultiPathfinder
{
    private $network;
    private $path;

    private function __construct(Network $network)
    {
        $this->network = $network;
        $this->path = new PathRetracer();
    }

    public static function operatingIn(Network $graph): MultiPathfinder
    {
        return new self($graph);
    }

    public function from(string $start): array
    {
        return $this->shortestPaths($this->startingAt($start), $start);
    }

    private function shortestPaths(array $lastSteps, string $start): array
    {
        $paths = [];
        foreach ($lastSteps as $goal => $lastStep) {
            if ($goal !== $start) {
                $paths[$goal] = $this->path->retrace($start, $goal, $lastSteps);
            }
        }
        return $paths;
    }

    /** @throws NoPathAvailable */
    private function startingAt(string $start): array
    {
        if (!$this->network->has($start)) {
            throw NonExistingStart::tried($start);
        }

        $distanceTo = [$start => 0];
        $lastStepBefore = [];

        $underConsideration = new SplPriorityQueue();
        $underConsideration->insert($start, 0);

        while (!$underConsideration->isEmpty()) {
            $node = $underConsideration->top();
            $underConsideration->next();

            foreach ($this->network->neighboursOf($node) as $neighbour) {
                $alternativeCost = ($distanceTo[$node] ?? INF) +
                    $this->network->movementCostBetween($node, $neighbour);

                if ($alternativeCost < ($distanceTo[$neighbour] ?? INF)) {
                    $distanceTo[$neighbour] = $alternativeCost;
                    $lastStepBefore[$neighbour] = $node;
                    $underConsideration->insert($neighbour, -$alternativeCost);
                }
            }
        }
        return $lastStepBefore;
    }
}
