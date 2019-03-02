<?php

namespace Stratadox\Pathfinder;

use InvalidArgumentException;

/**
 * Network.
 *
 * A network is a collection of nodes that are connected through edges.
 *
 * @api
 * @author Stratadox
 */
interface Network
{
    /**
     * Retrieves the labels of all of the nodes that are in the network.
     *
     * @return Labels The labels of all the nodes in the network.
     */
    public function all(): Labels;

    /**
     * Retrieves the labels of the neighbours of the node with the given label.
     *
     * @param string $node The label of the node whose neighbours to retrieve.
     * @return Labels      The labels of the neighbouring nodes.
     */
    public function neighboursOf(string $node): Labels;

    /**
     * Checks whether the nodes are neighbours.
     *
     * Note that the network is directed by default: as such this operation is
     * not necessarily symmetric: A can be a neighbour of B, without B being a
     * neighbour of A.
     *
     * @param string $source    The label that identifies the source node.
     * @param string $neighbour The label that identifies the potential neighbour.
     * @return bool             Whether the nodes are neighbours.
     */
    public function areNeighbours(string $source, string $neighbour): bool;

    /**
     * Checks whether a node with the given label exists in the network.
     *
     * @param string $node The label to look for.
     * @return bool        Whether the label is present.
     */
    public function has(string $node): bool;

    /**
     * Retrieves the movement cost between two neighbours.
     *
     * @param string $source            The label of the source node.
     * @param string $neighbour         The label of the neighbouring node.
     * @return float                    The cost of the edge.
     * @throws InvalidArgumentException When the nodes are not neighbours.
     */
    public function movementCostBetween(string $source, string $neighbour): float;

    /**
     * Checks whether the network contains negative edge costs.
     *
     * @return bool Whether the network contains edges with negative costs.
     */
    public function hasNegativeEdgeCosts(): bool;
}
