<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Distance;

use function sqrt;
use Stratadox\Pathfinder\Metric;
use Stratadox\Pathfinder\Position;

final class Euclidean implements Metric
{
    private $dimensions;

    public function __construct(int $dimensions)
    {
        $this->dimensions = $dimensions;
    }

    public static function distance(): Metric
    {
        return new self(2);
    }

    public static function inDimensions(int $amount): Metric
    {
        return new self($amount);
    }

    public function distanceBetween(Position $start, Position $goal): float
    {
        $sum = 0;
        for ($i = $this->dimensions - 1; $i >= 0; --$i) {
            $sum += ($start[$i] - $goal[$i]) ** 2;
        }
        return sqrt($sum);
    }
}
