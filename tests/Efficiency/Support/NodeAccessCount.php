<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Test\Efficiency\Support;

use function func_get_args;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Labels;
use Stratadox\Pathfinder\Position;

final class NodeAccessCount implements Environment
{
    private $count = [];
    private $graph;

    public function __construct(Environment $graph)
    {
        $this->graph = $graph;
    }

    public static function decorating(Environment $graph): self
    {
        return new self($graph);
    }

    public function all(): Labels
    {
        return $this->invoke(__FUNCTION__, ...func_get_args());
    }

    public function positionOf(string $node): Position
    {
        return $this->invoke(__FUNCTION__, ...func_get_args());
    }

    public function neighboursOf(string $node): Labels
    {
        return $this->invoke(__FUNCTION__, ...func_get_args());
    }

    public function areNeighbours(string $source, string $neighbour): bool
    {
        return $this->invoke(__FUNCTION__, ...func_get_args());
    }

    public function has(string $node): bool
    {
        return $this->invoke(__FUNCTION__, ...func_get_args());
    }

    public function movementCostBetween(string $source, string $neighbour): float
    {
        return $this->invoke(__FUNCTION__, ...func_get_args());
    }

    public function hasNegativeEdgeCosts(): bool
    {
        return $this->invoke(__FUNCTION__, ...func_get_args());
    }

    public function counted(string $function): int
    {
        return $this->count[$function] ?? 0;
    }

    public function reset(): void
    {
        $this->count = [];
    }

    private function invoke(string $method, ...$arguments)
    {
        $this->count[$method] = isset($this->count[$method]) ?
            $this->count[$method]+ 1 :
            1;
        return $this->graph->{$method}(...$arguments);
    }
}
