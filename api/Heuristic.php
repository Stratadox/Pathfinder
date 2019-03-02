<?php

namespace Stratadox\Pathfinder;

/**
 * Heuristic.
 *
 * Heuristics provide estimations for the cost of a path between two nodes.
 *
 * @api
 * @author Stratadox
 */
interface Heuristic
{
    /**
     * Estimates the cost for the path between a given start and goal.
     *
     * Implementations may use any @see Metric in any number of dimensions in
     * order to arrive at a potentially sensible guesstimate, or derive a result
     * in any other fashion. The resulting estimate should be interpreted as
     * estimation, and not be relied upon as absolute truth.
     *
     * @param string $start The label that identifies the start node.
     * @param string $goal  The label that identifies the goal node.
     * @return float        The estimated cost of the path.
     */
    public function estimate(string $start, string $goal): float;

    /**
     * Retrieves the environment this heuristic operates on.
     *
     * @return Environment
     */
    public function environment(): Environment;
}
