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
     * Finds the shortest path from a given start node to a particular goal node.
     *
     * @param string $start    The label of the start node.
     * @param string $goal     The label of the goal node.
     * @return string[]        The labels of the nodes that form the path.
     * @throws NoPathAvailable When no possible paths are available.
     */
    public function between(string $start, string $goal): array;
}
