<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Estimate;

use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Heuristic;

final class Safely implements Heuristic
{
    private $heuristic;
    private $environment;

    private function __construct(
        Heuristic $heuristic,
        Environment $environment
    ) {
        $this->heuristic = $heuristic;
        $this->environment = $environment;
    }

    public static function apply(Heuristic $heuristic): self
    {
        return new self($heuristic, $heuristic->environment());
    }

    public function estimate(string $start, string $goal): float
    {
        if ($this->environment->areNeighbours($start, $goal)) {
            return $this->environment->movementCostBetween($start, $goal);
        }
        return $this->heuristic->estimate($start, $goal);
    }

    public function environment(): Environment
    {
        return $this->environment;
    }
}
