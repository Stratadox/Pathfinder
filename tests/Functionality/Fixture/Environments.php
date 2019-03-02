<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Functionality\Fixture;

use Stratadox\Pathfinder\Distance\Euclidean;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Graph\At;
use Stratadox\Pathfinder\Graph\Builder\GraphEnvironment;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;
use Stratadox\Pathfinder\Graph\Builder\WithoutEdges;

final class Environments
{
    public function fromExample(): Environment
    {
        return GraphEnvironment::create()
            ->withLocation('A', At::position(0, 0), WithEdge::to('B', 5)->andTo('C', 8))
            ->withLocation('B', At::position(0, 4), WithEdge::to('D', 9)->andTo('A', 1))
            ->withLocation('C', At::position(4, 0), WithEdge::to('D', 4)->andTo('A', 1))
            ->withLocation('D', At::position(4, 4), WithEdge::to('B', 3)->andTo('C', 9))
            ->make();
    }

    public function edgeless(): Environment
    {
        return GraphEnvironment::create()
            ->withLocation('A', At::position(0, 0), WithoutEdges::poorThing())
            ->withLocation('B', At::position(0, 4), WithoutEdges::poorThing())
            ->withLocation('C', At::position(4, 0), WithoutEdges::poorThing())
            ->withLocation('D', At::position(4, 4), WithoutEdges::poorThing())
            ->make();
    }

    public function threeDimensional(): Environment
    {
        return GraphEnvironment::create()
            ->withLocation(
                'A',
                At::position(0, 0, 0),
                WithEdge::toTargets('B', 'C')
            )
            ->withLocation(
                'B',
                At::position(0, 2, 9),
                WithEdge::toTargets('A', 'C', 'D')
            )
            ->withLocation(
                'C',
                At::position(2, 0, 0),
                WithEdge::toTargets('D', 'A')
            )
            ->withLocation(
                'D',
                At::position(2, 2, 0),
                WithEdge::toTargets('B', 'C')
            )
            ->determineEdgeCostsAs(Euclidean::inDimensions(3))
            ->make();
    }

    public function fourDimensional(): Environment
    {
        return GraphEnvironment::create()
            ->withLocation(
                'A',
                At::position(0, 0, 0, 0),
                WithEdge::to('B', 13.6014)->andTo('C', 2)
            )
            ->withLocation(
                'B',
                At::position(0, 2, 9, 10),
                WithEdge::to('A', 13.6014)
                    ->andTo('C', 13.7477)
                    ->andTo('D', 9.2195)
            )
            ->withLocation(
                'C',
                At::position(2, 0, 0, 0),
                WithEdge::to('D', 10.1980)->andTo('A', 2)
            )
            ->withLocation(
                'D',
                At::position(2, 2, 0, 10),
                WithEdge::to('B', 9.2195)->andTo('C', 10.1980)
            )
            ->make();
    }
}
