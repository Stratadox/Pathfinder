<?php

namespace Stratadox\Pathfinder;

use Stratadox\Collection\Collection;

/**
 * Labels.
 *
 * A collection of labels.
 *
 * @api
 * @author Stratadox
 */
interface Labels extends Collection
{
    /**
     * Retrieves the label at the current position in the list.
     *
     * @return string The label that identifies the node in the network.
     */
    public function current(): string;
}
