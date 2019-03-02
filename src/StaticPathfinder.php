<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

final class StaticPathfinder implements Pathfinder
{
    private $index;
    private $network;

    private function __construct(ShortestPathForest $index, Network $network)
    {
        $this->index = $index;
        $this->network = $network;
    }

    public static function using(
        ShortestPathForest $index,
        Network $network
    ): Pathfinder {
        return new self($index, $network);
    }

    public function from(string $start): array
    {
        if (!$this->network->has($start)) {
            throw NonExistingStart::tried($start);
        }

        $paths = [];
        foreach ($this->network->all() as $goal) {
            if ($start !== $goal) {
                try {
                    $paths[$goal] = $this->between($start, $goal);
                } catch (NoPathAvailable $unreachableNode) {
                    // the node is not reachable: simply leave it out.
                }
            }
        }
        return $paths;
    }

    public function between(string $start, string $goal): array
    {
        $path = [$start];
        $node = $start;
        while ($node && $node !== $goal) {
            $node = $this->index->nextStepOnTheRoadBetween($node, $goal);
            $path[] = $node;
        }
        return $path;
    }
}
