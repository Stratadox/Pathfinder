<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use function count;
use const INF;

final class BellmanFordPathfinder implements MultiPathfinder
{
    private $network;

    private function __construct(Network $network)
    {
        $this->network = $network;
    }

    public static function operatingIn(Network $graph): MultiPathfinder
    {
        return new self($graph);
    }

    public function from(string $start): array
    {
        return $this->shortestPaths($this->startingAt($start), $start);
    }

    private function shortestPaths(array $lastStepsBefore, string $start): array
    {
        $paths = [];
        foreach ($lastStepsBefore as $goal => $lastStep) {
            if ($goal !== $start) {
                $path = [$goal];
                while (isset($lastStepsBefore[$lastStep])) {
                    $path[] = $lastStep;
                    $lastStep = $lastStepsBefore[$lastStep];
                }
                $path[] = $start;
                $paths[$goal] = array_reverse($path);
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

        /** @var string[] $nodes */
        $nodes = $this->network->all()->items();
        for ($i = count($nodes); $i > 0; --$i) {
            foreach ($nodes as $from) {
                foreach ($this->network->neighboursOf($from) as $to) {
                    $proposal = ($distanceTo[$from] ?? INF) +
                        $this->network->movementCostBetween($from, $to);
                    if ($proposal < ($distanceTo[$to] ?? INF)) {
                        $distanceTo[$to] = $proposal;
                        $lastStepBefore[$to] = $from;
                    }
                }
            }
        }
        foreach ($nodes as $from) {
            foreach ($this->network->neighboursOf($from) as $to) {
                $proposal = ($distanceTo[$from] ?? INF) +
                    $this->network->movementCostBetween($from, $to);
                if ($proposal < ($distanceTo[$to] ?? INF)) {
                    throw NegativeCycleDetected::amongTheNodes($from, $to);
                }
            }
        }
        return $lastStepBefore;
    }
}
