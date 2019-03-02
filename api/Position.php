<?php

namespace Stratadox\Pathfinder;

use ArrayAccess;

/**
 * Position.
 *
 * A position is a geographical location in Euclidean space, represented as a
 * set of Cartesian coordinates.
 *
 * @api
 * @author Stratadox
 */
interface Position extends ArrayAccess
{
    public function offsetGet($offset): float;
}
