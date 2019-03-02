<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Estimate;

use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Heuristic;

final class FromPreviousEnvironment implements Heuristic
{
    private $heuristic;
    private $environment;

    private function __construct(Heuristic $heuristic, Environment $environment)
    {
        $this->heuristic = $heuristic;
        $this->environment = $environment;
    }

    public static function state(
        Heuristic $heuristic,
        Environment $environment
    ): Heuristic {
        return new self($heuristic, $environment);
    }

    public function estimate(string $start, string $goal): float
    {
        return $this->heuristic->estimate($start, $goal);
    }

    public function environment(): Environment
    {
        return $this->environment;
    }
}
