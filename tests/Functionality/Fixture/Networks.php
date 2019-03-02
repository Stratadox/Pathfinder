<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Functionality\Fixture;

use Stratadox\Pathfinder\Graph\Builder\GraphNetwork;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;
use Stratadox\Pathfinder\Graph\Builder\WithoutEdges;
use Stratadox\Pathfinder\Network;

final class Networks
{
    public function fromExample(): Network
    {
        return GraphNetwork::create()
            ->withVertex('A', WithEdge::to('B', 5)->andTo('C', 8))
            ->withVertex('B', WithEdge::to('D', 9)->andTo('A', 1))
            ->withVertex('C', WithEdge::to('D', 4)->andTo('A', 1))
            ->withVertex('D', WithEdge::to('B', 3)->andTo('C', 9))
            ->make();
    }

    public function edgeless(): Network
    {
        return GraphNetwork::create()
            ->withVertex('A', WithoutEdges::poorThing())
            ->withVertex('B', WithoutEdges::poorThing())
            ->withVertex('C', WithoutEdges::poorThing())
            ->withVertex('D', WithoutEdges::poorThing())
            ->make();
    }

    public function withNegativeCycles(): Network
    {
        return GraphNetwork::create()
            ->withVertex('A', WithEdge::to('B', 1))
            ->withVertex('B', WithEdge::to('C', 0)->andTo('E', 1))
            ->withVertex('C', WithEdge::to('D', 0))
            ->withVertex('D', WithEdge::to('B', -1))
            ->withVertex('E', WithoutEdges::poorThing())
            ->make();
    }
}
