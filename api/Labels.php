<?php

namespace Stratadox\Pathfinder;

use Stratadox\Collection\Reducible;

/**
 * Labels.
 *
 * A collection of labels.
 *
 * @api
 * @author Stratadox
 */
interface Labels extends Reducible
{
    public function current(): string;
}
