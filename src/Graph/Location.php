<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

use Stratadox\Pathfinder\Position;

final class Location implements GeometricVertex
{
    private $position;
    private $label;
    private $edges;

    private function __construct(Position $position, string $label, Edges $edges)
    {
        $this->position = $position;
        $this->label = $label;
        $this->edges = $edges;
    }

    public static function at(
        Position $position,
        string $label,
        Edges $edges = null
    ): GeometricVertex {
        return new self($position, $label, $edges ?: Roads::none());
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function edges(): Edges
    {
        return $this->edges;
    }

    public function hasNegativeEdges(): bool
    {
        foreach ($this->edges() as $edge) {
            if ($edge->cost() < 0) {
                return true;
            }
        }
        return false;
    }
}
