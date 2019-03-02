<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph\Builder;

use function array_merge;
use InvalidArgumentException;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Graph\GeometricGraph;
use Stratadox\Pathfinder\Graph\Location;
use Stratadox\Pathfinder\Graph\Road;
use Stratadox\Pathfinder\Graph\Roads;
use Stratadox\Pathfinder\Metric;
use Stratadox\Pathfinder\Position;

final class GraphEnvironment
{
    private $metric;
    private $locations;

    private function __construct(?Metric $metric, Location ...$locations)
    {
        $this->metric = $metric;
        $this->locations = $locations;
    }

    public static function create(): self
    {
        return new self(null);
    }

    public function withLocation(
        string $label,
        Position $position,
        EdgeDefinition $edges
    ): self {
        return new self($this->metric, ...array_merge(
            $this->locations,
            [Location::at($position, $label, $edges->gather())]
        ));
    }

    public function determineEdgeCostsAs(Metric $metric): self
    {
        return new self($metric, ...$this->locations);
    }

    public function make(): Environment
    {
        return GeometricGraph::with(...$this->actualLocations());
    }

    private function actualLocations(): array
    {
        if (null === $this->metric) {
            return $this->locations;
        }
        $actualLocations = [];
        foreach ($this->locations as $i => $location) {
            $newEdges = [];
            foreach ($location->edges() as $edge) {
                $newEdges[] = Road::towards($edge->target(), $this->newCost(
                    $edge->cost(),
                    $this->metric->distanceBetween(
                        $location->position(),
                        $this->positionOf($edge->target())
                    )
                ));
            }
            $actualLocations[] = Location::at(
                $location->position(),
                $location->label(),
                Roads::available(...$newEdges)
            );
        }
        return $actualLocations;
    }

    private function newCost(float $previousCost, float $distance): float
    {
        return $previousCost - 1.0 + $distance;
    }

    private function positionOf(string $node): Position
    {
        foreach ($this->locations as $location) {
            if ($node === $location->label()) {
                return $location->position();
            }
        }
        throw new InvalidArgumentException("$node does not exist!");
    }
}
