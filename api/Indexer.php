<?php

namespace Stratadox\Pathfinder;

/**
 * Indexer.
 *
 * Produces indices for the environment, allowing for efficient pathfinding.
 *
 * @api
 * @author Stratadox
 */
interface Indexer
{
    /**
     * Builds an index of all shortest paths.
     *
     * This can be a relatively slow process. It is advised to perform this
     * operation only at key points in time, for instance when deploying the
     * application or before publication of new environments.
     *
     * @return ShortestPathForest
     */
    public function allShortestPaths(): ShortestPathForest;

    /**
     * Produces a heuristic that knows the shortest path distances.
     *
     * Such heuristic is "fully informed", in the sense that it knows exactly
     * how far it takes to get from any node to any other node, at least in the
     * environment it was produced for.
     *
     * Using this heuristic is recommended in case the environment is large and
     * changes only slightly over time. For such environments, a fully informed
     * heuristic of the initial state of the environment can be made in advance,
     * used not as absolute truth but as guideline to find the then-shortest
     * path.
     *
     * Similar to @see allShortestPaths, this is a potentially slow operation,
     * best performed at key moments only.
     *
     * @return Heuristic
     */
    public function heuristic(): Heuristic;
}
