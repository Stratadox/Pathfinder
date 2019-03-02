<?php
namespace Stratadox\Pathfinder;

/**
 * Metric.
 *
 * A metric is a mechanism to measure the distance between two points.
 *
 * @api
 * @author Stratadox
 */
interface Metric
{
    /**
     * Retrieves the distance between two points.
     *
     * The start and goal vertices must have the same number of dimensions.
     *
     * @param Position $start The start coordinates.
     * @param Position $goal  The goal coordinates.
     * @return float          The distance between the vertices.
     */
    public function distanceBetween(Position $start, Position $goal): float;
}
