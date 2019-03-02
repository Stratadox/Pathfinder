<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Functionality;

use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\DynamicPathfinder;
use Stratadox\Pathfinder\NoPathAvailable;
use Stratadox\Pathfinder\Test\Functionality\Fixture\Networks;

/**
 * @testdox Finding the shortest path between two nodes in non-geometric graphs
 */
class ShortestPathBetweenTwoNodes extends TestCase
{
    /** @var Networks */
    private $network;

    protected function setUp(): void
    {
        $this->network = new Networks();
    }

    /** @test */
    function finding_a_path_between_A_and_D()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->network->fromExample()
        );

        $this->assertSame(['A', 'C', 'D'], $shortestPath->between('A', 'D'));
    }

    /** @test */
    function finding_a_path_between_D_and_A()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->network->fromExample()
        );

        $this->assertSame(['D', 'B', 'A'], $shortestPath->between('D', 'A'));
    }

    /** @test */
    function finding_a_path_avoiding_negative_cycles()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->network->withNegativeCycles()
        );

        $this->assertSame(['A', 'B', 'E'], $shortestPath->between('A', 'E'));
    }

    /** @test */
    function cannot_find_a_path_if_there_is_no_path_available()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->network->edgeless()
        );

        $this->expectException(NoPathAvailable::class);
        $shortestPath->between('A', 'D');
    }

    /** @test */
    function cannot_find_a_path_if_the_start_does_not_exist()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->network->fromExample()
        );

        $this->expectException(NoPathAvailable::class);
        $shortestPath->between('Z', 'A');
    }

    /** @test */
    function cannot_find_a_path_if_the_goal_does_not_exist()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->network->fromExample()
        );

        $this->expectException(NoPathAvailable::class);
        $shortestPath->between('A', 'Z');
    }
}
