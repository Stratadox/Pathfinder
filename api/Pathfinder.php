<?php

namespace Stratadox\Pathfinder;

/**
 * Pathfinder.
 *
 * A pathfinder is a mechanism to find either the shortest path between a given
 * start and goal node, or all shortest paths from a given starting point.
 *
 * @api
 * @author Stratadox
 */
interface Pathfinder extends SinglePathfinder, MultiPathfinder
{
}
