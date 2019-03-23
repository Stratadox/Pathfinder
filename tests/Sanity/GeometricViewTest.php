<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Sanity;

use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\Graph\Builder\GraphNetwork;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;
use Stratadox\Pathfinder\Graph\GeometricView;
use Stratadox\Pathfinder\Graph\Ids;

/**
 * @testdox Sanity check to assert that the geometric view is a proper adapter
 */
class GeometricViewTest extends TestCase
{
    /** @test */
    function fetching_all_indices()
    {
        $graph = GraphNetwork::create()
            ->withVertex('A', WithEdge::to('B'))
            ->withVertex('B', WithEdge::to('A'))
            ->make();

        $environment = GeometricView::of($graph);

        $this->assertEquals(
            Ids::consistingOf('A', 'B'),
            $environment->all()
        );
    }

    /** @test */
    function detecting_negative_weights()
    {
        $graph = GraphNetwork::create()
            ->withVertex('A', WithEdge::to('B', -1.5))
            ->withVertex('B', WithEdge::to('A'))
            ->make();

        $environment = GeometricView::of($graph);

        $this->assertTrue($environment->hasNegativeEdgeCosts());
    }

    /** @test */
    function detecting_no_negative_weights()
    {
        $graph = GraphNetwork::create()
            ->withVertex('A', WithEdge::to('B'))
            ->withVertex('B', WithEdge::to('A'))
            ->make();

        $environment = GeometricView::of($graph);

        $this->assertFalse($environment->hasNegativeEdgeCosts());
    }
}
