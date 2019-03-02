<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Functionality;

use function implode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Estimate\Estimate;
use Stratadox\Pathfinder\Heuristic;
use Stratadox\Pathfinder\DynamicPathfinder;
use Stratadox\Pathfinder\NoPathAvailable;
use Stratadox\Pathfinder\Distance\Chebyshev;
use Stratadox\Pathfinder\Distance\Euclidean;
use Stratadox\Pathfinder\Distance\Taxicab;
use Stratadox\Pathfinder\Test\Functionality\Fixture\Environments;
use Stratadox\Pathfinder\Test\Functionality\Fixture\Grids;

/**
 * @testdox Finding the shortest path between two locations in geometric environments
 */
class ShortestPathBetweenTwoLocations extends TestCase
{
    private const EUCLIDEAN = 1;
    private const TAXICAB = 2;
    private const CHEBYSHEV = 3;

    /** @var Environments */
    private $environment;
    /** @var Grids */
    private $grid;

    protected function setUp(): void
    {
        $this->environment = new Environments();
        $this->grid = new Grids();
    }

    /** @test */
    function finding_a_path_between_A_and_D()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->fromExample()
        );

        $this->assertSame(['A', 'C', 'D'], $shortestPath->between('A', 'D'));
    }

    /** @test */
    function finding_a_path_between_D_and_A()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->fromExample()
        );

        $this->assertSame(['D', 'B', 'A'], $shortestPath->between('D', 'A'));
    }

    /** @test */
    function finding_a_path_between_A_and_D_on_a_3D_map()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->threeDimensional()
        );

        $this->assertSame(['A', 'C', 'D'], $shortestPath->between('A', 'D'));
    }

    /** @test */
    function finding_a_path_between_A_and_D_on_a_4D_map()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->fourDimensional()
        );

        $this->assertSame(['A', 'C', 'D'], $shortestPath->between('A', 'D'));
    }

    /** @test */
    function finding_a_path_between_B1_and_B3()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->grid->fromExample()
        );

        $this->assertSame(
            ['B1', 'A1', 'A2', 'A3', 'B3'],
            $shortestPath->between('B1', 'B3')
        );
    }

    /** @test */
    function finding_a_path_between_B1_and_B3_with_diagonals_allowed()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->grid->allowingDiagonals()
        );

        $this->assertSame(
            ['B1', 'A2', 'B3'],
            $shortestPath->between('B1', 'B3')
        );
    }

    /** @test */
    function finding_a_path_between_B1_and_B3_when_the_A_route_costs_extra()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->grid->weighed()
        );

        $this->assertSame(
            ['B1', 'C1', 'D1', 'D2', 'D3', 'C3', 'B3'],
            $shortestPath->between('B1', 'B3')
        );
    }

    /** @test */
    function finding_a_path_between_B1_and_B3_in_a_grid_with_negative_costs()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->grid->negativelyWeighed()
        );

        $this->assertSame(
            ['B1', 'A1', 'A2', 'A3', 'B3'],
            $shortestPath->between('B1', 'B3')
        );
    }

    /** @test */
    function finding_a_path_on_a_relatively_wide_grid()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->grid->wide()
        );

        $pathAsString = implode(' -> ', $shortestPath->between('A1', 'AB1'));

        $this->assertStringStartsWith(
            'A1 -> B1 -> C1 -> D1 -> E1 -> F1 ->',
            $pathAsString
        );
        $this->assertStringEndsWith(
            '-> X1 -> Y1 -> Z1 -> AA1 -> AB1',
            $pathAsString
        );
    }

    /** @test */
    function cannot_find_a_path_if_there_is_no_path_available()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->edgeless()
        );

        $this->expectException(NoPathAvailable::class);
        $shortestPath->between('A', 'D');
    }

    /** @test */
    function cannot_find_a_path_if_the_start_does_not_exist()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->fromExample()
        );

        $this->expectException(NoPathAvailable::class);
        $shortestPath->between('Z', 'A');
    }

    /** @test */
    function cannot_find_a_path_if_the_goal_does_not_exist()
    {
        $shortestPath = DynamicPathfinder::operatingIn(
            $this->environment->fromExample()
        );

        $this->expectException(NoPathAvailable::class);
        $shortestPath->between('A', 'Z');
    }

    /**
     * @test
     * @dataProvider heuristics
     */
    function finding_a_path_between_B1_and_B3_using_different_heuristics(
        int $heuristic,
        int $dimensions
    ) {
        $shortestPath = DynamicPathfinder::withHeuristic(
            $this->heuristic(
                $heuristic,
                $dimensions,
                $this->grid->fromExample()
            )
        );

        $this->assertSame(
            ['B1', 'A1', 'A2', 'A3', 'B3'],
            $shortestPath->between('B1', 'B3')
        );
    }

    public function heuristics(): array
    {
        return [
            'Euclidean 2D' => [self::EUCLIDEAN, 2],
            'Euclidean 3D' => [self::EUCLIDEAN, 3],
            'Euclidean 4D' => [self::EUCLIDEAN, 4],
            'Euclidean 42D' => [self::EUCLIDEAN, 42],
            'Taxicab 2D' => [self::TAXICAB, 2],
            'Taxicab 3D' => [self::TAXICAB, 3],
            'Taxicab 4D' => [self::TAXICAB, 4],
            'Taxicab 8D' => [self::TAXICAB, 8],
            'Chebyshev 2D' => [self::CHEBYSHEV, 2],
            'Chebyshev 3D' => [self::CHEBYSHEV, 3],
            'Chebyshev 4D' => [self::CHEBYSHEV, 4],
            'Chebyshev 150D' => [self::CHEBYSHEV, 150],
        ];
    }

    private function heuristic(
        int $which,
        int $dimensions,
        Environment $environment
    ): Heuristic {
        $useSimpleConstructor = $dimensions === 2;
        switch ($which) {
            case self::EUCLIDEAN: return Estimate::costAs(
                $useSimpleConstructor ?
                    Euclidean::distance() :
                    Euclidean::inDimensions($dimensions),
                $environment
            );
            case self::TAXICAB: return Estimate::costAs(
                $useSimpleConstructor ?
                    Taxicab::distance() :
                    Taxicab::inDimensions($dimensions),
                $environment
            );
            case self::CHEBYSHEV: return Estimate::costAs(
                $useSimpleConstructor ?
                    Chebyshev::distance() :
                    Chebyshev::inDimensions($dimensions),
                $environment
            );
        }
        throw new InvalidArgumentException('No such heuristic.');
    }
}
