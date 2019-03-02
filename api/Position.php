<?php

namespace Stratadox\Pathfinder;

use ArrayAccess;

/**
 * Position.
 *
 * A position is a geographical point in Euclidean space, represented as a set
 * of Cartesian coordinates.
 *
 * @api
 * @author Stratadox
 */
interface Position extends ArrayAccess
{
    /**
     * Retrieves the position along the given axis.
     *
     * @param int $axis The offset of the axis.
     * @return float    The position along the axis.
     */
    public function offsetGet($axis): float;
}
