<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use RuntimeException;

final class NonExistingStart extends RuntimeException implements NoPathAvailable
{
    public static function tried(string $start): NoPathAvailable
    {
        return new self("Cannot find a path from non-existing $start.");
    }
}
