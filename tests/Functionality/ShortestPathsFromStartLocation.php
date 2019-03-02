<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Functionality;

use function asort;
use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\DynamicPathfinder;
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;
use Stratadox\Pathfinder\NoPathAvailable;
use Stratadox\Pathfinder\Test\Functionality\Fixture\Environments;

/**
 * @testdox Finding all shortest paths from a start location
 */
class ShortestPathsFromStartLocation extends TestCase
{
    /** @var Environments */
    private $environment;

    protected function setUp(): void
    {
        $this->environment = new Environments();
    }

    /** @test */
    function finding_all_shortest_paths_starting_at_A()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->fromExample()
        );

        $shortestPathFromA = $shortestPath->from('A');

        $this->assertCount(3, $shortestPathFromA);
        $this->assertSame(['A', 'B'], $shortestPathFromA['B']);
        $this->assertSame(['A', 'C'], $shortestPathFromA['C']);
        $this->assertSame(['A', 'C', 'D'], $shortestPathFromA['D']);
    }

    /** @test */
    function finding_all_shortest_paths_starting_at_A1()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            GridEnvironment::fromArray([
                [1.0, 1.2],
                [1.0, 1.0],
            ])->make()
        );

        $shortestPathFromA1 = $shortestPath->from('A1');

        $this->assertCount(3, $shortestPathFromA1);
        $this->assertSame(['A1', 'A2'], $shortestPathFromA1['A2']);
        $this->assertSame(['A1', 'B1'], $shortestPathFromA1['B1']);
        $this->assertSame(['A1', 'A2', 'B2'], $shortestPathFromA1['B2']);
    }

    /** @test */
    function finding_all_shortest_paths_when_some_nodes_are_unreachable()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            GridEnvironment::fromArray([
                [1.0, 1.2, INF, INF],
                [1.0, 1.0, INF, 1.0],
            ])->make()
        );

        $shortestPathFromA1 = $shortestPath->from('A1');

        $this->assertCount(3, $shortestPathFromA1);
        $this->assertSame(['A1', 'A2'], $shortestPathFromA1['A2']);
        $this->assertSame(['A1', 'B1'], $shortestPathFromA1['B1']);
        $this->assertSame(['A1', 'A2', 'B2'], $shortestPathFromA1['B2']);
    }

    /** @test */
    function cannot_find_a_path_if_the_start_does_not_exist()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->fromExample()
        );

        $this->expectException(NoPathAvailable::class);
        $shortestPath->from('Z');
    }
}
