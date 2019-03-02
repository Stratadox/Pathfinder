<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph\Builder;

use function array_merge;
use Stratadox\Pathfinder\Graph\Graph;
use Stratadox\Pathfinder\Graph\Node;
use Stratadox\Pathfinder\Network;

final class GraphNetwork
{
    private $nodes;

    private function __construct(Node ...$locations)
    {
        $this->nodes = $locations;
    }

    public static function create(): self
    {
        return new self();
    }

    public function withVertex(
        string $label,
        EdgeDefinition $edges
    ): self {
        return new self(...array_merge(
            $this->nodes,
            [Node::labeled($label, $edges->gather())]
        ));
    }

    public function make(): Network
    {
        return Graph::with(...$this->nodes);
    }
}
