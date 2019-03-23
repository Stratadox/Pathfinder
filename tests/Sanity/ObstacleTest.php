<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Sanity;

use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\Graph\Builder\Obstacle;

/**
 * @testdox Sanity check to assert that obstacles have infinite costs
 */
class ObstacleTest extends TestCase
{
    /** @test */
    function obstacles_have_infinite_costs()
    {
        $obstacle = Obstacle::here();

        $this->assertInfinite($obstacle->price());
    }

    /** @test */
    function repriced_obstacles_still_have_infinite_costs()
    {
        $obstacle = Obstacle::here()->costing(1.0);

        $this->assertInfinite($obstacle->price());
    }

    /** @test */
    function obstacles_do_not_have_labels()
    {
        $obstacle = Obstacle::here();

        $this->assertEmpty($obstacle->label());
    }
}
