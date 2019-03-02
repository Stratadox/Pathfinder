<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph\Builder;

use Stratadox\Pathfinder\Graph\Edges;
use Stratadox\Pathfinder\Graph\Road;
use Stratadox\Pathfinder\Graph\Roads;

final class WithEdge implements EdgeDefinition
{
    private $targetLabel;
    private $edgeCost;

    private function __construct(string $targetLabel, float $edgeCost)
    {
        $this->targetLabel = $targetLabel;
        $this->edgeCost = $edgeCost;
    }

    public static function to(
        string $targetLabel,
        float $edgeCost = 1.0
    ): EdgeDefinition {
        return new self($targetLabel, $edgeCost);
    }

    public static function toTargets(
        string $targetLabel,
        string ...$moreLabels
    ): EdgeDefinition {
        $edge = self::to($targetLabel);
        foreach ($moreLabels as $label) {
            $edge = $edge->andTo($label);
        }
        return $edge;
    }

    public function andTo(string $target, float $cost = 1.0): EdgeDefinition
    {
        return MultipleEdges::consistingOf($this, WithEdge::to($target, $cost));
    }

    public function gather(): Edges
    {
        return Roads::available(
            Road::towards($this->targetLabel, $this->edgeCost)
        );
    }
}
