<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Efficiency;

use function asort;
use const INF;
use function key;
use function microtime;
use PHPUnit\Framework\TestCase;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Estimate\Estimate;
use Stratadox\Pathfinder\Graph\GeometricView;
use Stratadox\Pathfinder\Network;
use Stratadox\Pathfinder\SinglePathfinder;
use Stratadox\Pathfinder\AStarPathfinder;
use Stratadox\Pathfinder\Distance\Chebyshev;
use Stratadox\Pathfinder\Distance\Euclidean;
use Stratadox\Pathfinder\Distance\Taxicab;
use Stratadox\Pathfinder\Graph\At;
use Stratadox\Pathfinder\Graph\Builder\GraphEnvironment;
use Stratadox\Pathfinder\Graph\Builder\GraphNetwork;
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;
use Stratadox\Pathfinder\SingleDijkstraPathfinder;
use Stratadox\Pathfinder\Test\Efficiency\Support\NodeAccessCount;

/**
 * @testdox Determining the fastest algorithms for finding single paths
 *
 * Flaky tests be here.
 */
class SinglePath extends TestCase
{
    private const ITERATIONS = 80;
    private $speedTest;

    protected function setUp(): void
    {
        $this->speedTest = $_SERVER['speed_test'] ?? false;
    }

    /**
     * @test
     * @dataProvider grids
     */
    function finding_a_path(
        Network $network,
        string $start,
        string $goal,
        string $expectedWinner,
        string $graphType
    ) {
        $environment = $network instanceof Environment ?
            $network :
            GeometricView::of($network);
        $nodeAccess = NodeAccessCount::decorating($environment);
        $contestants = [
            'Dijkstra' => SingleDijkstraPathfinder::operatingIn(
                $nodeAccess
            ),
            'A* with 2D Euclidean' => AStarPathfinder::withHeuristic(
                Estimate::costAs(Euclidean::distance(), $nodeAccess)
            ),
            'A* with 2D Taxicab' => AStarPathfinder::withHeuristic(
                Estimate::costAs(Taxicab::distance(), $nodeAccess)
            ),
            'A* with 2D Chebyshev' => AStarPathfinder::withHeuristic(
                Estimate::costAs(Chebyshev::distance(), $nodeAccess)
            ),
            'A* with 3D Euclidean' => AStarPathfinder::withHeuristic(
                Estimate::costAs(Euclidean::inDimensions(3), $nodeAccess)
            ),
            'A* with 3D Taxicab' => AStarPathfinder::withHeuristic(
                Estimate::costAs(Taxicab::inDimensions(3), $nodeAccess)
            ),
            'A* with 3D Chebyshev' => AStarPathfinder::withHeuristic(
                Estimate::costAs(Chebyshev::inDimensions(3), $nodeAccess)
            ),
        ];

        $result = [];
        foreach ($contestants as $name => $shortestPath) {
            $time = $this->manyTimes(
                $this->speedTest ? self::ITERATIONS : 1,
                $shortestPath,
                $start,
                $goal
            );
            $result[$name] = $this->speedTest ?
                $time :
                $nodeAccess->counted('neighboursOf');
            $nodeAccess->reset();
        }

        $this->assertFastest(
            $result,
            $expectedWinner,
            $graphType,
            $this->speedTest ? 'seconds' : 'iterations'
        );
    }

    private function assertFastest(
        array $result,
        string $expectedWinner,
        string $graphType,
        string $metric
    ): void {
        asort($result);

        $actualWinner = key($result);
        $bestTime = $result[$actualWinner];
        $time = $result[$expectedWinner];
        $difference = $time - $bestTime;
        $percentage = $bestTime !== 0.0 ?
            round($difference / $bestTime * 100, 2) :
            0;

        $scoreboard = '';
        foreach ($result as $name => $t) {
            $scoreboard .= "$name used $t $metric\n";
        }

        $this->assertTrue(
            $time <= $bestTime,
            "Expecting $expectedWinner to be the best performing algorithm " .
            "for the $graphType, but $actualWinner was fastest, with $bestTime " .
            "against $expectedWinner's $time; a $percentage% difference over " .
            self::ITERATIONS . " iterations.\n\n\nWinner on $graphType: " .
            $scoreboard
        );
    }

    private function manyTimes(
        int $iterations,
        SinglePathfinder $finder,
        string $start,
        string $goal
    ): float {
        $time = -microtime(true);

        for ($i = $iterations; $i > 0; --$i) {
            $finder->between($start, $goal);
        }

        $time += microtime(true);

        return $time;
    }

    public function grids(): iterable
    {
        $gridEnvironment = GridEnvironment::fromArray([
            [1.0, INF, 1.0, 1.1, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0],
            [1.0, 1.2, 1.0, INF, 1.2, 1.0, 1.0, 1.0, 1.0, 1.2, 1.0, 1.0],
            [1.0, INF, 1.1, 1.1, INF, 1.1, INF, 1.1, 1.0, INF, 1.1, INF],
            [1.0, 1.0, 1.1, 1.1, 1.0, 1.1, 1.0, 1.1, 1.0, 1.0, 1.1, 1.0],
            [1.0, 1.0, 1.1, 1.2, 1.1, INF, INF, 1.1, 1.1, 1.1, INF, INF],
            [1.0, INF, 1.2, INF, 1.2, 1.0, 1.0, 1.2, 1.0, 1.2, 1.0, 1.0],
            [1.0, 1.2, 1.0, 1.2, 1.0, INF, 1.0, 1.0, INF, 1.0, INF, 1.0],
            [1.0, 1.0, 1.1, 1.1, 1.0, 1.1, 1.0, 1.1, 1.0, 1.0, 1.1, 1.0],
            [1.0, INF, 1.0, 1.0, 1.0, INF, 1.0, 1.0, 1.2, 1.0, INF, 1.0],
            [1.0, 1.2, 1.0, 1.0, 1.0, 1.2, 1.0, 1.0, INF, 1.0, INF, 1.0],
            [1.0, 1.0, 1.1, 1.1, 1.0, 1.1, 1.0, 1.1, 1.0, 1.0, 1.1, 1.0],
            [1.0, 1.0, 1.1, 1.2, 1.1, INF, 1.2, 1.1, 1.1, 1.1, INF, INF],
            [1.0, INF, 1.0, 1.1, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0],
            [1.0, 1.2, 1.0, INF, 1.2, 1.0, 1.0, 1.0, 1.0, 1.2, 1.0, 1.0],
            [1.0, INF, 1.1, 1.1, INF, 1.1, 1.2, 1.1, 1.0, INF, 1.1, INF],
            [1.0, 1.0, 1.1, 1.1, 1.0, 1.1, 1.0, 1.1, 1.0, 1.0, 1.1, 1.0],
            [1.0, 1.0, 1.1, 1.2, 1.1, 1.2, 1.2, 1.1, 1.1, 1.1, INF, INF],
            [1.0, INF, 1.2, INF, 1.2, 1.0, 1.0, 1.2, 1.0, 1.2, 1.0, 1.0],
            [1.0, 1.2, 1.0, 1.2, 1.0, INF, 1.0, 1.0, INF, 1.0, INF, 1.0],
            [1.0, 1.0, 1.1, 1.1, 1.0, 1.1, 1.0, 1.1, 1.0, 1.0, 1.1, 1.0],
            [1.0, INF, 1.0, 1.0, 1.0, INF, 1.0, 1.0, INF, 1.0, INF, 1.0],
            [1.0, 1.2, 1.0, 1.0, 1.0, INF, 1.0, 1.0, INF, 1.0, INF, 1.0],
            [1.0, 1.0, 1.1, 1.1, 1.0, 1.1, 1.0, 1.1, 1.0, 1.0, 1.1, 1.0],
            [1.0, 1.0, 1.1, 1.2, 1.1, INF, INF, 1.1, 1.1, 1.1, INF, INF],
        ])->make();
        // @todo these graphs are relatively small, making the tests very flaky.
        // @todo make GraphLoader and fetch the data from a json(?) file.
        $graphEnvironment2D = GraphEnvironment::create()
            ->withLocation('A', At::position(0.5, 0), WithEdge::toTargets('B', 'F'))
            ->withLocation('B', At::position(5, 1), WithEdge::toTargets('A', 'C', 'G'))
            ->withLocation('C', At::position(7, 0), WithEdge::toTargets('B', 'D'))
            ->withLocation('D', At::position(10, 1), WithEdge::toTargets('C', 'E', 'J'))
            ->withLocation('E', At::position(12, 2), WithEdge::toTargets('D', 'K'))
            ->withLocation('F', At::position(0, 3), WithEdge::toTargets('A', 'H'))
            ->withLocation('G', At::position(6, 3), WithEdge::toTargets('B', 'J', 'L'))
            ->withLocation('H', At::position(1, 4), WithEdge::toTargets('F', 'I'))
            ->withLocation('I', At::position(5, 4), WithEdge::toTargets('H'))
            ->withLocation('J', At::position(8, 4), WithEdge::toTargets('D', 'G', 'L', 'N'))
            ->withLocation('K', At::position(15, 5), WithEdge::toTargets('E', 'R'))
            ->withLocation('L', At::position(6, 5.5), WithEdge::toTargets('G', 'J', 'P', 'O'))
            ->withLocation('M', At::position(0, 6), WithEdge::toTargets('H', 'O', 'W'))
            ->withLocation('N', At::position(9, 6), WithEdge::toTargets('J', 'L', 'P', 'Q'))
            ->withLocation('O', At::position(2, 7), WithEdge::toTargets('M', 'L', 'P', 'S'))
            ->withLocation('P', At::position(7, 8), WithEdge::toTargets('L', 'N', 'O', 'S', 'T'))
            ->withLocation('Q', At::position(10, 8), WithEdge::toTargets('N', 'T', 'U'))
            ->withLocation('R', At::position(15, 8), WithEdge::toTargets('K', 'U', 'V'))
            ->withLocation('S', At::position(6, 10), WithEdge::toTargets('O', 'P', 'T'))
            ->withLocation('T', At::position(9, 11), WithEdge::toTargets('P', 'Q', 'S'))
            ->withLocation('U', At::position(12, 12), WithEdge::toTargets('Q', 'R', 'V', 'X'))
            ->withLocation('V', At::position(18, 12), WithEdge::toTargets('R', 'U'))
            ->withLocation('W', At::position(0, 13), WithEdge::toTargets('M', 'Y'))
            ->withLocation('X', At::position(11, 14), WithEdge::toTargets('U', 'Z'))
            ->withLocation('Y', At::position(4, 15), WithEdge::toTargets('W', 'Z'))
            ->withLocation('Z', At::position(4, 15), WithEdge::toTargets('X', 'Y'))
            ->determineEdgeCostsAs(Euclidean::distance())
            ->make();
        $network = GraphNetwork::create()
            ->withVertex('A', WithEdge::toTargets('B', 'F'))
            ->withVertex('B', WithEdge::toTargets('A', 'C', 'G'))
            ->withVertex('C', WithEdge::toTargets('B', 'D'))
            ->withVertex('D', WithEdge::toTargets('C', 'E', 'J'))
            ->withVertex('E', WithEdge::toTargets('D', 'K'))
            ->withVertex('F', WithEdge::toTargets('A', 'H'))
            ->withVertex('G', WithEdge::toTargets('B', 'J', 'L'))
            ->withVertex('H', WithEdge::toTargets('F', 'I'))
            ->withVertex('I', WithEdge::toTargets('H'))
            ->withVertex('J', WithEdge::toTargets('D', 'G', 'L', 'N'))
            ->withVertex('K', WithEdge::toTargets('E', 'R'))
            ->withVertex('L', WithEdge::toTargets('G', 'J', 'P', 'O'))
            ->withVertex('M', WithEdge::toTargets('H', 'O', 'W'))
            ->withVertex('N', WithEdge::toTargets('J', 'L', 'P', 'Q'))
            ->withVertex('O', WithEdge::toTargets('M', 'L', 'P', 'S'))
            ->withVertex('P', WithEdge::toTargets('L', 'N', 'O', 'S', 'T'))
            ->withVertex('Q', WithEdge::toTargets('N', 'T', 'U'))
            ->withVertex('R', WithEdge::toTargets('K', 'U', 'V'))
            ->withVertex('S', WithEdge::toTargets('O', 'P', 'T'))
            ->withVertex('T', WithEdge::toTargets('P', 'Q', 'S'))
            ->withVertex('U', WithEdge::toTargets('Q', 'R', 'V', 'X'))
            ->withVertex('V', WithEdge::toTargets('R', 'U'))
            ->withVertex('W', WithEdge::toTargets('M', 'Y'))
            ->withVertex('X', WithEdge::toTargets('U', 'Z'))
            ->withVertex('Y', WithEdge::toTargets('W', 'Z'))
            ->withVertex('Z', WithEdge::toTargets('X', 'Y'))
            ->make();
        return [
            'Non-diagonal grid, A1 to K23. Expecting A* Taxicab to win.' => [
                $gridEnvironment,
                'A1',
                'K23',
                'A* with 2D Taxicab',
                'non-diagonal grid',
            ],
            'Geographic graph, from A to Q. Expecting A* Euclidean to win.'=> [
                $graphEnvironment2D,
                'A',
                'Q',
                'A* with 2D Euclidean',
                'geographic 2D graph',
            ],
            'Non-geographic graph, from A to Q. Expecting Dijkstra to win.'=> [
                $network,
                'A',
                'Q',
                'Dijkstra',
                'non-geographic network',
            ],
        ];
    }
}
