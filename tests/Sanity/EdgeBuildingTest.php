<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Sanity;

use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\Graph\Builder\WithoutEdges;

/**
 * @testdox Sanity check to assert that edge creation behaves properly
 */
class EdgeBuildingTest extends TestCase
{
    /** @test */
    function zero_edges_plus_one_edge_is_one_edge()
    {
        $edges = WithoutEdges::poorThing()->andTo('A');

        $this->assertCount(1, $edges->gather());
    }
}
