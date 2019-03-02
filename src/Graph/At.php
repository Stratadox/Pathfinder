<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph;

use BadMethodCallException;
use Stratadox\Pathfinder\Position;

final class At implements Position
{
    private $coordinates;

    private function __construct(float ...$coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public static function position(float ...$coordinates): Position
    {
        return new self(...$coordinates);
    }

    public function offsetExists($offset): bool
    {
        return true;
    }

    public function offsetGet($offset): float
    {
        return $this->coordinates[$offset] ?? 0.0;
    }

    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Unsupported operation.');
    }

    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('Unsupported operation.');
    }
}
