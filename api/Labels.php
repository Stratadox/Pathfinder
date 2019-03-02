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
    /**
     * Retrieves the label at the current position in the list.
     *
     * @return string The label that identifies the node in the network.
     */
    public function current(): string;
}
