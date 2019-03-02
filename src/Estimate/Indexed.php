<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Estimate;

use const INF;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Heuristic;

final class Indexed implements Heuristic
{
    private $cache;
    private $environment;

    private function __construct(array $cache, Environment $environment)
    {
        $this->cache = $cache;
        $this->environment = $environment;
    }

    public static function heuristic(
        array $cache,
        Environment $environment
    ): Heuristic {
        return new self($cache, $environment);
    }

    public function estimate(string $start, string $goal): float
    {
        return $this->cache[$start][$goal] ?? INF;
    }

    public function environment(): Environment
    {
        return $this->environment;
    }
}
