<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Sanity;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\Graph\At;
use Stratadox\Pathfinder\Graph\Builder\GraphEnvironment;
use Stratadox\Pathfinder\Graph\Builder\GraphNetwork;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;

/**
 * @testdox Sanity check to assert that graphs behave properly
 */
class GraphTest extends TestCase
{
    /** @test */
    function cannot_get_cost_between_non_neighbouring_nodes_in_a_network()
    {
        $network = GraphNetwork::create()
            ->withVertex('A', WithEdge::to('B'))
            ->withVertex('B', WithEdge::to('C'))
            ->withVertex('C', WithEdge::to('A'))
            ->make();

        $this->expectException(InvalidArgumentException::class);

        $network->movementCostBetween('A', 'C');
    }

    /** @test */
    function cannot_get_cost_between_non_neighbouring_nodes_in_an_environment()
    {
        $network = GraphEnvironment::create()
            ->withLocation('A', At::position(0, 0), WithEdge::to('B'))
            ->withLocation('B', At::position(0, 1), WithEdge::to('C'))
            ->withLocation('C', At::position(0, 2), WithEdge::to('A'))
            ->make();

        $this->expectException(InvalidArgumentException::class);

        $network->movementCostBetween('A', 'C');
    }
}
