<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Sanity;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use function random_int;
use Stratadox\Pathfinder\Graph\At;

/**
 * @testdox Sanity check to assert that positions are infinite and immutable
 */
class AtPositionTest extends TestCase
{
    /**
     * @test
     * @dataProvider dimensionAndCoordinates
     */
    function all_dimensions_are_accessible(int $dimension, float ...$coordinates)
    {
        $position = At::position(...$coordinates);

        $this->assertTrue(isset($position[$dimension]));
    }

    /**
     * @test
     * @dataProvider dimensionAndCoordinates
     */
    function cannot_write_to_positions(int $dimension, float ...$coordinates)
    {
        $position = At::position(...$coordinates);
        $number = random_int(100, 1000) / random_int(10, 1000);

        $this->expectException(BadMethodCallException::class);

        $position[$dimension] = $number;
    }

    /**
     * @test
     * @dataProvider dimensionAndCoordinates
     */
    function cannot_unset_positions(int $dimension, float ...$coordinates)
    {
        $position = At::position(...$coordinates);

        $this->expectException(BadMethodCallException::class);

        unset($position[$dimension]);
    }

    public function dimensionAndCoordinates(): iterable
    {
        $random = random_int(10, 90000);
        return [
            'First dimension of [1,1]' => [0, 1, 1],
            'Third dimension of [1,1]' => [2, 1, 1],
            'Fifth dimension of [5,3]' => [4, 5, 3],
            'Fifth dimension of [5,3,4]' => [4, 5, 3, 4],
            $random.'th dimension of [5,3,4]' => [$random, 5, 3, 4],
        ];
    }
}
