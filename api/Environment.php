<?php

namespace Stratadox\Pathfinder;

/**
 * Environment.
 *
 * An environment is a graph with geometric information associated to its nodes.
 *
 * @api
 * @author Stratadox
 */
interface Environment extends Network
{
    /**
     * Retrieves the position of the node with the associated label.
     *
     * @param string $node The label of the node whose position to retrieve.
     * @return Position    The position of the node associated with the label.
     */
    public function positionOf(string $node): Position;
}
