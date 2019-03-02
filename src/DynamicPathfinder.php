<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use Stratadox\Pathfinder\Distance\Euclidean;
use Stratadox\Pathfinder\Estimate\Estimate;
use Stratadox\Pathfinder\Estimate\Safely;
use Stratadox\Pathfinder\Graph\GeometricView;

final class DynamicPathfinder implements Pathfinder
{
    private $singlePath;
    private $multiPath;

    private function __construct(
        SinglePathfinder $singlePath,
        MultiPathfinder $multiPath
    ) {
        $this->singlePath = $singlePath;
        $this->multiPath = $multiPath;
    }

    public static function operatingIn(Network $environment): Pathfinder
    {
        return new self(
            self::singlePathfinder($environment),
            MultiDijkstraPathfinder::operatingIn($environment)
        );
    }

    public static function withHeuristic(Heuristic $heuristic): self
    {
        return new self(
            self::singlePathfinder($heuristic->environment(), $heuristic),
            MultiDijkstraPathfinder::operatingIn($heuristic->environment())
        );
    }

    private static function singlePathfinder(
        Network $theWorld,
        Heuristic $heuristic = null
    ): SinglePathfinder {
        if ($theWorld instanceof Environment) {
            return AStarPathfinder::withHeuristic($heuristic ?: Safely::apply(
                Estimate::costAs(Euclidean::distance(), $theWorld)
            ));
        }
        if ($theWorld->hasNegativeEdgeCosts()) {
            return AStarPathfinder::withHeuristic($heuristic ?: Safely::apply(
                Estimate::costAs(Euclidean::distance(), GeometricView::of($theWorld))
            ));
        }
        return SingleDijkstraPathfinder::operatingIn($theWorld);
    }

    public function from(string $start): array
    {
        return $this->multiPath->from($start);
    }

    public function between(string $start, string $goal): array
    {
        return $this->singlePath->between($start, $goal);
    }
}
