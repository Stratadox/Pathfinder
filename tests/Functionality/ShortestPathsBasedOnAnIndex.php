<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Functionality;

use const INF;
use Stratadox\Pathfinder\FloydWarshallIndexer;
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;
use Stratadox\Pathfinder\NoPathAvailable;
use Stratadox\Pathfinder\StaticPathfinder;
use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\Test\Functionality\Fixture\Environments;
use Stratadox\Pathfinder\Test\Functionality\Fixture\Networks;

/**
 * @testdox Finding all shortest paths in a network, using an index
 */
class ShortestPathsBasedOnAnIndex extends TestCase
{
    /** @var Environments */
    private $environment;

    /** @var Networks */
    private $network;

    protected function setUp(): void
    {
        $this->environment = new Environments();
        $this->network = new Networks();
    }

    /** @test */
    function building_an_index_of_next_nodes_on_the_shortest_paths()
    {
        $environment = GridEnvironment::fromArray([
            [1.0, 1.0, 1.0, 1.0],
            [1.0, INF, INF, 1.0],
            [1.0, 1.0, 1.0, 1.0],
        ])->make();

        $index = FloydWarshallIndexer::operatingIn($environment)->allShortestPaths();

        // 'B1', 'A1', 'A2', 'A3', 'B3'
        $this->assertSame('A1', $index->nextStepOnTheRoadBetween('B1', 'B3'));
        $this->assertSame('A2', $index->nextStepOnTheRoadBetween('A1', 'B3'));
        $this->assertSame('A3', $index->nextStepOnTheRoadBetween('A2', 'B3'));
        $this->assertSame('B3', $index->nextStepOnTheRoadBetween('A3', 'B3'));
    }

    /** @test */
    function building_an_index_of_next_nodes_on_the_cheapest_paths()
    {
        $environment = GridEnvironment::fromArray([
            [5.0, 1.0, 1.0, 1.0],
            [5.0, INF, INF, 1.0],
            [5.0, 1.0, 1.0, 1.0],
        ])->make();

        $index = FloydWarshallIndexer::operatingIn($environment)->allShortestPaths();

        // 'B1', 'C1', 'D1', 'D2', 'D3', 'C3', 'B3'
        $this->assertSame('C1', $index->nextStepOnTheRoadBetween('B1', 'B3'));
        $this->assertSame('D1', $index->nextStepOnTheRoadBetween('C1', 'B3'));
        $this->assertSame('D2', $index->nextStepOnTheRoadBetween('D1', 'B3'));
        $this->assertSame('D3', $index->nextStepOnTheRoadBetween('D2', 'B3'));
        $this->assertSame('C3', $index->nextStepOnTheRoadBetween('D3', 'B3'));
        $this->assertSame('B3', $index->nextStepOnTheRoadBetween('C3', 'B3'));
    }

    /** @test */
    function constructing_a_path_based_on_the_information_from_the_index()
    {
        $environment = GridEnvironment::fromArray([
            [1.0, 1.0, 1.0, 1.0],
            [1.0, INF, INF, 1.0],
            [1.0, 1.0, 1.0, 1.0],
        ])->make();

        $shortestPath = StaticPathfinder::using(
            FloydWarshallIndexer::operatingIn($environment)->allShortestPaths(),
            $environment
        );

        $this->assertSame(
            ['B1', 'A1', 'A2', 'A3', 'B3'],
            $shortestPath->between('B1', 'B3')
        );
        $this->assertSame(
            ['C1', 'D1', 'D2', 'D3', 'C3'],
            $shortestPath->between('C1', 'C3')
        );
    }

    /** @test */
    function constructing_a_path_based_on_the_information_of_the_network_index()
    {
        $network = $this->network->fromExample();

        $shortestPath = StaticPathfinder::using(
            FloydWarshallIndexer::operatingIn($network)->allShortestPaths(),
            $network
        );

        $this->assertSame(['A', 'C', 'D'], $shortestPath->between('A', 'D'));
        $this->assertSame(['D', 'B', 'A'], $shortestPath->between('D', 'A'));
    }

    /** @test */
    function indexing_a_network_with_negative_cycles()
    {
        $network = $this->network->withNegativeCycles();

        $shortestPath = StaticPathfinder::using(
            FloydWarshallIndexer::operatingIn($network)->allShortestPaths(),
            $network
        );

        $this->assertSame(
            ['A', 'B', 'C', 'D'],
            $shortestPath->between('A', 'D'),
            'Indexing should work fine, but once the static pathfinder ' .
            'encounters a cyclical reference, it will loop forever.'
        );
    }

    /** @test */
    function constructing_all_paths_from_start_node_A_based_on_an_index()
    {
        $environment = $this->environment->fromExample();

        $shortestPath = StaticPathfinder::using(
            FloydWarshallIndexer::operatingIn($environment)->allShortestPaths(),
            $environment
        );

        $shortestPathFromA = $shortestPath->from('A');
        $this->assertCount(3, $shortestPathFromA);
        $this->assertSame(['A', 'B'], $shortestPathFromA['B']);
        $this->assertSame(['A', 'C'], $shortestPathFromA['C']);
        $this->assertSame(['A', 'C', 'D'], $shortestPathFromA['D']);
    }

    /** @test */
    function constructing_all_paths_from_start_node_A1_based_on_an_index()
    {
        $environment = GridEnvironment::fromArray([
            [1.0, 1.2],
            [1.0, 1.0],
        ])->make();

        $shortestPath = StaticPathfinder::using(
            FloydWarshallIndexer::operatingIn($environment)->allShortestPaths(),
            $environment
        );

        $shortestPathFromA1 = $shortestPath->from('A1');

        $this->assertCount(3, $shortestPathFromA1);
        $this->assertSame(['A1', 'A2'], $shortestPathFromA1['A2']);
        $this->assertSame(['A1', 'B1'], $shortestPathFromA1['B1']);
        $this->assertSame(['A1', 'A2', 'B2'], $shortestPathFromA1['B2']);
    }

    /** @test */
    function constructing_all_paths_from_start_with_unreachable_node()
    {
        $environment = GridEnvironment::fromArray([
            [1.0, 1.2, INF, INF],
            [1.0, 1.0, INF, 1.0],
        ])->make();

        $shortestPath = StaticPathfinder::using(
            FloydWarshallIndexer::operatingIn($environment)->allShortestPaths(),
            $environment
        );

        $shortestPathFromA1 = $shortestPath->from('A1');

        $this->assertCount(3, $shortestPathFromA1);
        $this->assertSame(['A1', 'A2'], $shortestPathFromA1['A2']);
        $this->assertSame(['A1', 'B1'], $shortestPathFromA1['B1']);
        $this->assertSame(['A1', 'A2', 'B2'], $shortestPathFromA1['B2']);
    }

    /** @test */
    function cannot_find_a_path_if_there_is_no_path_available()
    {
        $environment = GridEnvironment::fromArray([
            [1.0, 1.0],
            [INF, INF],
            [1.0, 1.0],
        ])->make();

        $shortestPath = StaticPathfinder::using(
            FloydWarshallIndexer::operatingIn($environment)->allShortestPaths(),
            $environment
        );

        $this->expectException(NoPathAvailable::class);

        $shortestPath->between('A1', 'A3');
    }

    /** @test */
    function cannot_find_all_paths_from_a_non_existing_start_node()
    {
        $environment = GridEnvironment::fromArray([
            [1.0, 1.0],
            [1.0, 1.0],
        ])->make();

        $shortestPath = StaticPathfinder::using(
            FloydWarshallIndexer::operatingIn($environment)->allShortestPaths(),
            $environment
        );

        $this->expectException(NoPathAvailable::class);

        $shortestPath->from('C6');
    }

    /** @test */
    function producing_an_exact_heuristic()
    {
        $environment = GridEnvironment::fromArray([
            [1.0, 1.0, 1.0, 1.0],
            [1.0, INF, INF, 1.0],
            [1.0, 1.0, 1.0, 1.0],
        ])->make();

        $heuristic = FloydWarshallIndexer::operatingIn($environment)->heuristic();

        $this->assertSame(2.0, $heuristic->estimate('A1', 'A3'));
        $this->assertSame(4.0, $heuristic->estimate('B1', 'B3'));
        $this->assertSame(INF, $heuristic->estimate('B1', 'X3456'));
    }
}
