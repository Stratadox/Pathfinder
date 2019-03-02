<?php

namespace Stratadox\Pathfinder;

/**
 * Multiple paths finder.
 *
 * A multi-path finder is a pathfinder that finds the shortest paths from a
 * given start node to *all* other (reachable) nodes.
 *
 * @api
 * @author Stratadox
 */
interface MultiPathfinder
{
    /**
     * Finds the shortest paths from a given start to all possible goals.
     *
     * @param string $start    A string representation of the label that
     *                         identifies the start node.
     * @return string[][]      A map of arrays as [goal => path], where path is
     *                         an array of the labels of the nodes that form the
     *                         shortest path.
     * @throws NoPathAvailable When the start node does not exist.
     */
    public function from(string $start): array;
}
