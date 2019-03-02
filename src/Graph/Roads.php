<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

use Stratadox\ImmutableCollection\Appending;
use Stratadox\ImmutableCollection\ImmutableCollection;
use Stratadox\ImmutableCollection\Merging;
use Stratadox\Pathfinder\Graph\Edge;
use Stratadox\Pathfinder\Graph\Edges;

final class Roads extends ImmutableCollection implements Edges
{
    use Appending, Merging;

    protected function __construct(Road ...$roads)
    {
        parent::__construct(...$roads);
    }

    public static function available(Road ...$roads): Edges
    {
        return new self(...$roads);
    }

    public static function none(): Edges
    {
        return new self();
    }

    public function current(): Edge
    {
        return parent::current();
    }
}
