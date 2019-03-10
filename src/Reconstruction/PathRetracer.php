<?php declare(strict_types=1);

namespace Stratadox\Pathfinder\Reconstruction;

final class PathRetracer
{
    public function retrace(
        string $start,
        string $goal,
        array $breadcrumbs
    ): array {
        $node = $goal;
        $path = [];
        while (isset($breadcrumbs[$node])) {
            $path[] = $node;
            $node = $breadcrumbs[$node];
        }
        $path[] = $start;
        return array_reverse($path);
    }
}
