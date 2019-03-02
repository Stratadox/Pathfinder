<?php declare(strict_types=1);

namespace Stratadox\Pathfinder;

use RuntimeException;

final class NoSuchPath extends RuntimeException implements NoPathAvailable
{
    public static function between(string $start, string $goal): NoPathAvailable
    {
        return new self("No possible path between $start and $goal.");
    }
}
