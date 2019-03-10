<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use function implode;
use RuntimeException;
use function sprintf;

final class NegativeCycleDetected extends RuntimeException implements NoPathAvailable
{
    public static function amongTheNodes(string ...$nodes): NoPathAvailable
    {
        return new self(sprintf(
            'Detected a negative cycle near the nodes: %s.',
            implode(', ', $nodes)
        ));
    }
}
