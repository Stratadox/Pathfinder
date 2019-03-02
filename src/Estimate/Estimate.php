<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Estimate;

use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Heuristic;
use Stratadox\Pathfinder\Metric;

final class Estimate implements Heuristic
{
    private $environment;
    private $metric;

    private function __construct(
        Metric $metric,
        Environment $environment
    ) {
        $this->metric = $metric;
        $this->environment = $environment;
    }

    public static function costAs(
        Metric $metric,
        Environment $environment
    ): Heuristic {
        return new static($metric, $environment);
    }

    public function estimate(string $start, string $goal): float
    {
        return $this->metric->distanceBetween(
            $this->environment->positionOf($start),
            $this->environment->positionOf($goal)
        );
    }

    public function environment(): Environment
    {
        return $this->environment;
    }
}
