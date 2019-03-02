<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Graph\Builder;

use function array_merge;
use function array_reduce;
use function chr;
use Stratadox\Pathfinder\Environment;
use Stratadox\Pathfinder\Graph\At;
use Stratadox\Pathfinder\Graph\Edges;
use Stratadox\Pathfinder\Graph\GeometricGraph;
use Stratadox\Pathfinder\Graph\Location;
use Stratadox\Pathfinder\Graph\Road;
use Stratadox\Pathfinder\Graph\Roads;

final class GridEnvironment
{
    private const MOVEMENT = [
        [
            ['x' => -1, 'y' => 0],
            ['x' => 0, 'y' => -1],
            ['x' => 0, 'y' => 1],
            ['x' => 1, 'y' => 0],
        ],
        [
            ['x' => -1, 'y' => 0],
            ['x' => -1, 'y' => -1],
            ['x' => 1, 'y' => -1],
            ['x' => 0, 'y' => -1],
            ['x' => 0, 'y' => 1],
            ['x' => 1, 'y' => 1],
            ['x' => 1, 'y' => -1],
            ['x' => 1, 'y' => 0],
        ],
    ];

    /** @var Field[][] */
    private $rows;
    private $allowDiagonal;

    private function __construct(bool $allowDiagonal, ...$rows)
    {
        $this->allowDiagonal = $allowDiagonal;
        $this->rows = $rows;
    }

    public static function create(): self
    {
        return new self(false);
    }

    public static function fromArray(array $gridData): self
    {
        $grid = self::create();
        foreach ($gridData as $row => $columns) {
            $squares = [];
            foreach ($columns as $column => $price) {
                $label = self::textual($column) . ($row + 1);
                $squares[] = $price !== INF
                    ? Square::labeled($label)->costing($price)
                    : Obstacle::here();
            }
            $grid = $grid->withRow(...$squares);
        }
        return $grid;
    }

    private static function textual(int $column): string
    {
        $finalPart = $column % 26;
        $letter = chr(65 + $finalPart);
        $firstPart = (int) ($column / 26);
        if (!$firstPart) {
            return $letter;
        }
        return self::textual($firstPart - 1) . $letter;
    }

    public function withRow(Field ...$squares): self
    {
        return new self(
            $this->allowDiagonal,
            ...array_merge($this->rows, [$squares])
        );
    }

    public function diagonalMovementAllowed(): self
    {
        return new self(true, ...$this->rows);
    }

    public function make(): Environment
    {
        $locations = [];
        foreach ($this->rows as $row => $fields) {
            foreach ($fields as $column => $field) {
                if ($field->isBlocked()) {
                    continue;
                }
                $locations[] = Location::at(
                    At::position($column, $row),
                    $field->label(),
                    $this->edgesStartingAt($row, $column)
                );
            }
        }
        return GeometricGraph::with(...$locations);
    }

    private function edgesStartingAt(int $row, int $column): Edges
    {
        return Roads::available(...array_reduce(
            self::MOVEMENT[$this->allowDiagonal],
            function (Edges $result, array $add) use ($row, $column): Edges {
                return $this->addIfAvailable(
                    $result,
                    $row + $add['x'],
                    $column + $add['y']
                );
            },
            Roads::none()
        ));
    }

    private function addIfAvailable(Edges $edges, int $row, int $column): Edges
    {
        return $this->isAvailable($row, $column)
            ? $edges->add(Road::towards(
                $this->targetAt($row, $column),
                $this->costOfGettingTo($row, $column)
            ))
            : $edges;
    }

    private function isAvailable(int $row, int $column): bool
    {
        return isset($this->rows[$row][$column])
            && !$this->rows[$row][$column]->isBlocked();
    }

    private function targetAt(int $row, int $column): string
    {
        return $this->rows[$row][$column]->label();
    }

    private function costOfGettingTo(int $row, int $column): float
    {
        return $this->rows[$row][$column]->price();
    }
}
