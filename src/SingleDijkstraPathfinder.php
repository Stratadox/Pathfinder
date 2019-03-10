<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use const INF;
use SplPriorityQueue;
use Stratadox\Pathfinder\Reconstruction\PathRetracer;

final class SingleDijkstraPathfinder implements SinglePathfinder
{
    private $network;
    private $path;

    private function __construct(Network $network)
    {
        $this->network = $network;
        $this->path = new PathRetracer();
    }

    public static function operatingIn(Network $graph): SinglePathfinder
    {
        return new self($graph);
    }

    public function between(string $start, string $goal): array
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

            if ($goal === $node) {
                return $this->path->retrace($start, $goal, $lastStepBefore);
            }

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
        throw NoSuchPath::between($start, $goal);
    }
}
