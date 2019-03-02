<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

final class Index implements ShortestPathForest
{
    private $nextNodeOnTheRoad;

    private function __construct(array $nextNodeOnTheRoad)
    {
        $this->nextNodeOnTheRoad = $nextNodeOnTheRoad;
    }

    /**
     * @param string[][] $nextNodeOnTheRoad
     * @return ShortestPathForest
     */
    public static function of(array $nextNodeOnTheRoad): ShortestPathForest
    {
        return new self($nextNodeOnTheRoad);
    }

    public function nextStepOnTheRoadBetween(
        string $start,
        string $goal
    ): string {
        if (isset($this->nextNodeOnTheRoad[$start][$goal])) {
            return $this->nextNodeOnTheRoad[$start][$goal];
        }
        throw NoSuchPath::between($start, $goal);
    }
}
