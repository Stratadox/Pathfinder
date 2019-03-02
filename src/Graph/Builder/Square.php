<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph\Builder;

final class Square implements Field
{
    private $label;
    private $cost;

    private function __construct(string $label, float $cost)
    {
        $this->label = $label;
        $this->cost = $cost;
    }

    public static function labeled(string $label): self
    {
        return new self($label, 1.0);
    }

    public function isBlocked(): bool
    {
        return false;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function price(): float
    {
        return $this->cost;
    }

    public function costing(float $price): Field
    {
        return new self($this->label, $price);
    }
}
