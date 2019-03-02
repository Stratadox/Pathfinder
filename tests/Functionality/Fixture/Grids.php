<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Functionality\Fixture;

use function array_fill;
use const INF;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;
use Stratadox\Pathfinder\Graph\Builder\Obstacle;
use Stratadox\Pathfinder\Graph\Builder\Square;

final class Grids
{
    public function fromExample(): Environment
    {
        return GridEnvironment::create()
            ->withRow(
                Square::labeled('A1'),
                Square::labeled('B1'),
                Square::labeled('C1'),
                Square::labeled('D1')
            )
            ->withRow(
                Square::labeled('A2'),
                Obstacle::here(),
                Obstacle::here(),
                Square::labeled('D2')
            )
            ->withRow(
                Square::labeled('A3'),
                Square::labeled('B3'),
                Square::labeled('C3'),
                Square::labeled('D3')
            )
            ->make();
    }

    public function weighed(): Environment
    {
        return GridEnvironment::create()
            ->withRow(
                Square::labeled('A1')->costing(2),
                Square::labeled('B1'),
                Square::labeled('C1'),
                Square::labeled('D1')
            )
            ->withRow(
                Square::labeled('A2')->costing(10),
                Obstacle::here(),
                Obstacle::here(),
                Square::labeled('D2')
            )
            ->withRow(
                Square::labeled('A3')->costing(2),
                Square::labeled('B3'),
                Square::labeled('C3'),
                Square::labeled('D3')
            )
            ->make();
    }

    public function negativelyWeighed(): Environment
    {
        return GridEnvironment::create()
            ->withRow(
                Square::labeled('A1')->costing(-1),
                Square::labeled('B1'),
                Square::labeled('C1'),
                Square::labeled('D1')
            )
            ->withRow(
                Square::labeled('A2')->costing(-1),
                Obstacle::here(),
                Obstacle::here(),
                Square::labeled('D2')
            )
            ->withRow(
                Square::labeled('A3')->costing(-1),
                Square::labeled('B3'),
                Square::labeled('C3'),
                Square::labeled('D3')
            )
            ->make();
    }

    public function allowingDiagonals(): Environment
    {
        return GridEnvironment::fromArray([
            [1.0, 1.0, 1.0, 1.0],
            [1.0, INF, INF, 1.0],
            [1.0, 1.0, 1.0, 1.0],
        ])->diagonalMovementAllowed()->make();
    }

    public function wide(): Environment
    {
        return GridEnvironment::fromArray(
            array_fill(0, 3, array_fill(0, 30, 1.0))
        )->diagonalMovementAllowed()->make();
    }
}
