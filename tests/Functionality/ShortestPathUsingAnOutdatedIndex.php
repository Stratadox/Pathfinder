<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Functionality;

use const INF;
use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\DynamicPathfinder;
use Stratadox\Pathfinder\Estimate\FromPreviousEnvironment;
use Stratadox\Pathfinder\FloydWarshallIndexer;
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;

/**
 * @testdox Finding the shortest path(s) using a potentially outdated index.
 */
class ShortestPathUsingAnOutdatedIndex extends TestCase
{
    /** @test */
    function finding_a_path_in_a_slightly_changed_environment()
    {
        $originalEnvironment = GridEnvironment::fromArray([
            [1.0, 1.0, 1.0, 1.0],
            [1.0, INF, INF, 1.0],
            [1.0, 1.0, 1.0, 1.0],
        ])->make();

        $heuristic = FloydWarshallIndexer::operatingIn($originalEnvironment)
            ->heuristic();

        $newEnvironment = GridEnvironment::fromArray([
            [1.0, 1.0, 1.0, 1.0],
            [1.1, INF, INF, 1.0],
            [1.0, 1.0, 1.0, 1.0],
        ])->make();

        $shortestPath = DynamicPathfinder::withHeuristic(
            FromPreviousEnvironment::state($heuristic, $newEnvironment)
        );

        $this->assertSame(
            ['B1', 'A1', 'A2', 'A3', 'B3'],
            $shortestPath->between('B1', 'B3')
        );
    }

    /** @test */
    function finding_a_path_in_a_changed_environment()
    {
        $originalEnvironment = GridEnvironment::fromArray([
            [1.0, 1.0, 1.0, 1.0],
            [1.0, INF, INF, 1.0],
            [1.0, 1.0, 1.0, 1.0],
        ])->make();

        $heuristic = FloydWarshallIndexer::operatingIn($originalEnvironment)
            ->heuristic();

        $newEnvironment = GridEnvironment::fromArray([
            [1.0, 1.0, 1.0, 1.0],
            [INF, INF, INF, 1.0],
            [1.0, 1.0, 1.0, 1.0],
        ])->make();

        $shortestPath = DynamicPathfinder::withHeuristic(
            FromPreviousEnvironment::state($heuristic, $newEnvironment)
        );

        $this->assertSame(
            ['B1', 'C1', 'D1', 'D2', 'D3', 'C3', 'B3'],
            $shortestPath->between('B1', 'B3')
        );
    }
}
