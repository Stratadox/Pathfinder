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
     * @param string $start    The label of the start node.
     * @return string[][]      A map of arrays as [goal => path], where path is
     *                         an array of the labels of the nodes that form the
     *                         shortest path.
     * @throws NoPathAvailable When the shortest paths could not be calculated.
     */
    public function from(string $start): array;
}
