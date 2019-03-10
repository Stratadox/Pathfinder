<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

use function array_map;
use Stratadox\ImmutableCollection\ImmutableCollection;
use Stratadox\Pathfinder\Labels;

final class Ids extends ImmutableCollection implements Labels
{
    private function __construct(string ...$labels)
    {
        parent::__construct(...$labels);
    }

    public static function consistingOf(string ...$labels): Labels
    {
        return new self(...$labels);
    }

    public static function for(Vertex ...$vertices): Labels
    {
        return new self(...array_map(function (Vertex $vertex): string {
            return $vertex->label();
        }, $vertices));
    }

    public function current(): string
    {
        return parent::current();
    }
}
