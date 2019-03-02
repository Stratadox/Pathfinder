<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

final class Road implements Edge
{
    private $target;
    private $cost;

    private function __construct(string $target, float $cost)
    {
        $this->target = $target;
        $this->cost = $cost;
    }

    public static function towards(string $target, float $cost): Edge
    {
        return new self($target, $cost);
    }

    public function target(): string
    {
        return $this->target;
    }

    public function cost(): float
    {
        return $this->cost;
    }
}
