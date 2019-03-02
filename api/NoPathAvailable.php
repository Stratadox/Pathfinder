<?php

namespace Stratadox\Pathfinder;

use Throwable;

/**
 * No path available.
 *
 * Exception for when no paths exist between the requested start and goal.
 *
 * @api
 * @author Stratadox
 */
interface NoPathAvailable extends Throwable
{
}
