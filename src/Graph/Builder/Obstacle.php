<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph\Builder;

use const INF;

final class Obstacle implements Field
{
    public static function here(): Field
    {
        return new self();
    }

    public function isBlocked(): bool
    {
        return true;
    }

    public function label(): string
    {
        return '';
    }

    public function price(): float
    {
        return INF;
    }

    public function costing(float $price): Field
    {
        return $this; // priceless
    }
}
