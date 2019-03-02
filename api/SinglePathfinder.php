<?php

namespace Stratadox\Pathfinder;

/**
 * Single paths finder.
 *
 * A single-path finder is a pathfinder that finds the shortest paths from a
 * given start node to a given goal node.
 *
 * @api
 * @author Stratadox
 */
interface SinglePathfinder
{
    /**
     * @param string $start    A string representation of the label that
     *                         identifies the start node.
     * @param string $goal     A string representation of the label that
     *                         identifies the goal node.
     * @return string[]        The labels of the nodes that form the path.
     * @throws NoPathAvailable When no possible paths are available.
     */
    public function between(string $start, string $goal): array;
}
