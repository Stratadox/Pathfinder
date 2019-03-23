<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Sanity;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\Distance\Euclidean;
use Stratadox\Pathfinder\Graph\At;
use Stratadox\Pathfinder\Graph\Builder\GraphEnvironment;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;

/**
 * @testdox Sanity check to assert that graphs behave properly
 */
class GraphEnvironmentTest extends TestCase
{
    /** @test */
    function cannot_auto_determine_the_cost_for_an_edge_that_goes_nowhere()
    {
        $builder = GraphEnvironment::create()
            ->withLocation('A', At::position(0, 0), WithEdge::to('B'))
            ->determineEdgeCostsAs(Euclidean::distance());

        $this->expectException(InvalidArgumentException::class);

        $builder->make();
    }
}
