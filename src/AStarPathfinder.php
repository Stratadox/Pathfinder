<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use function array_reverse;
use const INF;
use SplPriorityQueue;

final class AStarPathfinder implements SinglePathfinder
{
    private $environment;
    private $heuristic;

    private function __construct(Environment $environment, Heuristic $heuristic)
    {
        $this->environment = $environment;
        $this->heuristic = $heuristic;
    }

    public static function withHeuristic(Heuristic $heuristic): self
    {
        return new self($heuristic->environment(), $heuristic);
    }

    public function between(string $start, string $goal): array
    {
        if (!$this->environment->has($start)) {
            throw NonExistingStart::tried($start);
        }
        if (!$this->environment->has($goal)) {
            throw NoSuchPath::between($start, $goal);
        }
        $alreadyVisited = [];
        $alreadyConsidered = [$start => true];
        $lastStepBefore = [];
        $distanceTo = [$start => 0];
        $underConsideration = new SplPriorityQueue();
        $underConsideration->insert($start, 0);

        while (!$underConsideration->isEmpty()) {
            $node = $underConsideration->top();
            $underConsideration->next();

            if ($goal === $node) {
                $path = [];
                while (isset($lastStepBefore[$node])) {
                    $path[] = $node;
                    $node = $lastStepBefore[$node];
                }
                $path[] = $start;
                return array_reverse($path);
            }

            foreach ($this->environment->neighboursOf($node) as $neighbour) {
                if (isset($alreadyVisited[$neighbour])) {
                    continue;
                }
                $alreadyVisited[$neighbour] = true;
                $cost = ($distanceTo[$node] ?? INF) +
                    $this->environment->movementCostBetween($node, $neighbour);

                if (!isset($alreadyConsidered[$neighbour])) {
                    $underConsideration->insert(
                        $neighbour,
                        -($cost + $this->heuristic->estimate($neighbour, $goal))
                    );
                    $alreadyConsidered[$neighbour] = true;
                    if ($cost < ($distanceTo[$neighbour] ?? INF)) {
                        $distanceTo[$neighbour] = $cost;
                        $lastStepBefore[$neighbour] = $node;
                    }
                }
            }
        }
        throw NoSuchPath::between($start, $goal);
    }
}
