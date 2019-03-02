<?php

namespace Stratadox\Pathfinder;

/**
 * Shortest path forest.
 *
 * A shortest path forest is a collection of shortest-path trees, the latter
 * being a spanning tree where the paths between any start to any goal are also
 * the shortest paths through the network.
 *
 * In less technical terms, it's a map to quickly look up the shortest paths.
 *
 * @see https://en.wikipedia.org/wiki/Shortest-path_tree
 *
 * @api
 * @author Stratadox
 */
interface ShortestPathForest
{
    /**
     * Retrieves the next node on the road towards a given goal.
     *
     * @param string $start    The label that identifies the start node.
     * @param string $goal     The label that identifies the goal node.
     * @return string          The label that identifies the next node.
     * @throws NoPathAvailable When no possible paths are available.
     */
    public function nextStepOnTheRoadBetween(string $start, string $goal): string;
}
