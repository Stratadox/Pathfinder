<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

final class Node implements Vertex
{
    private $label;
    private $edges;

    private function __construct(string $label, Edges $edges)
    {
        $this->label = $label;
        $this->edges = $edges;
    }

    public static function labeled(
        string $label,
        Edges $edges = null
    ): Vertex {
        return new self($label, $edges ?: Roads::none());
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
