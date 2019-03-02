# Pathfinder
A motion planning solution for PHP.

[![Build Status](https://travis-ci.org/Stratadox/Pathfinder.svg?branch=master)](https://travis-ci.org/Stratadox/Pathfinder)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/Pathfinder/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/Pathfinder?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/Pathfinder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/Pathfinder/?branch=master)

## Installation
Install using `composer require stratadox/pathfinder`

## Examples
Shortest path(s) through a graph:
```php
<?php
use Stratadox\Pathfinder\DynamicPathfinder;
use Stratadox\Pathfinder\Graph\At;
use Stratadox\Pathfinder\Graph\Builder\GraphEnvironment;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;

$environment = GraphEnvironment::create()
    ->withLocation('A', At::position(0, 0), WithEdge::to('B', 5)->andTo('C', 8))
    ->withLocation('B', At::position(0, 1), WithEdge::to('D', 9)->andTo('A', 1))
    ->withLocation('C', At::position(1, 0), WithEdge::to('D', 4)->andTo('A', 1))
    ->withLocation('D', At::position(1, 1), WithEdge::to('B', 3)->andTo('C', 9))
    ->make();

$shortestPath = DynamicPathfinder::operatingIn($environment);

assert(['A', 'C', 'D'] === $shortestPath->between('A', 'D'));
assert(['D', 'B', 'A'] === $shortestPath->between('D', 'A'));

assert([
    'B' => ['A', 'B'],
    'C' => ['A', 'C'],
    'D' => ['A', 'C', 'D'],
] === $shortestPath->from('A'));
```

Shortest path in a grid:
```php
<?php
use Stratadox\Pathfinder\DynamicPathfinder;
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;
use Stratadox\Pathfinder\Graph\Builder\Obstacle;
use Stratadox\Pathfinder\Graph\Builder\Square;

$environment = GridEnvironment::create()
    ->withRow(
       Square::labeled('A1'),
       Square::labeled('B1'),
       Square::labeled('C1'),
       Square::labeled('D1')
    )
    ->withRow(
       Square::labeled('A2'),
       Obstacle::here(),
       Obstacle::here(),
       Square::labeled('D2')
    )
    ->withRow(
       Square::labeled('A3'),
       Square::labeled('B3'),
       Square::labeled('C3'),
       Square::labeled('D3')
    )
    ->make();

$shortestPath = DynamicPathfinder::operatingIn($environment);

assert(
    ['B1', 'A1', 'A2', 'A3', 'B3'] === $shortestPath->between('B1', 'B3')
);
```

Complete set of *all* shortest paths:
```php
<?php
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;
use Stratadox\Pathfinder\FloydWarshallIndexer;
use Stratadox\Pathfinder\StaticPathfinder;

$environment = GridEnvironment::fromArray([
    [1.0, 1.0, 1.0, 1.0],
    [1.0, INF, INF, 1.0],
    [1.0, 1.0, 1.0, 1.0],
])->make();

// slow operation: perform at deploy-time
$index = FloydWarshallIndexer::operatingIn($environment)->allShortestPaths();

// very fast operations: for use at runtime
$shortestPath = StaticPathfinder::using($index, $environment);

assert(
    ['B1', 'A1', 'A2', 'A3', 'B3'] === $shortestPath->between('B1', 'B3')
);
```

## Features
The pathfinder module offers two kinds of features: to build up an environment, 
and to search shortest paths through that environment.
### Search Algorithms
There are many algorithms in existence that perform [graph traversals](https://en.wikipedia.org/wiki/Graph_traversal).
The Pathfinder module implements several.
#### Dijkstra
The original path finding solution: [Dijkstra's algorithm](https://en.wikipedia.org/wiki/Dijkstra%27s_algorithm)
is a [breadth-first search](https://en.wikipedia.org/wiki/Breadth-first_search) 
algorithm that makes no assumptions about unknown routes. (I.e. it is free of 
[heuristics](https://en.wikipedia.org/wiki/Heuristic_(computer_science)))

[![Illustration of Dijkstra's algorithm finding a path from a start node to a goal node](https://upload.wikimedia.org/wikipedia/commons/2/23/Dijkstras_progress_animation.gif)](https://en.wikipedia.org/wiki/Dijkstra%27s_algorithm#/media/File:Dijkstras_progress_animation.gif)

Using Dijkstra's algorithm can lead to better performance compared to [A*](#a*) 
in cases where no heuristics are available or applicable.

Examples of such scenarios are when the environment has no coordinates to base 
a heuristic ranking on, or when searching for the shortest paths to *all* 
possible end vertices from a particular start vertex or vice-versa.

#### A*
A quick algorithm for finding paths at runtime is the [A* search](https://en.wikipedia.org/wiki/A*_search_algorithm).

A* is a [best-first search](https://en.wikipedia.org/wiki/Best-first_search) 
algorithm that finds the shortest (cheapest) path by maintaining a 
[priority queue](https://en.wikipedia.org/wiki/Priority_queue) containing the 
considered nodes, using the cost of the path so far plus the estimated cost of 
the rest of the path as (inverse) priority indicator.
 
[![Illustration of A* search for finding path from a start node to a goal node](https://upload.wikimedia.org/wikipedia/commons/5/5d/Astar_progress_animation.gif)](https://en.wikipedia.org/wiki/A*_search_algorithm#/media/File:Astar_progress_animation.gif)

Using the A* algorithm can lead to better performance compared to [Dijkstra](#dijkstra)
in cases where [consistent heuristics](https://en.wikipedia.org/wiki/Consistent_heuristic)
are applicable.

By default, the [Euclidean distance](https://en.wikipedia.org/wiki/Euclidean_distance)
is used as heuristic (to estimate the cost of the rest of the path).
Alternatively, [Taxicab distance](https://en.wikipedia.org/wiki/Taxicab_geometry)
can be used, as well as [Chebyshev distance](https://en.wikipedia.org/wiki/Chebyshev_distance) 
or the result of the [Floyd-Warshall algorithm](#floyd-warshall).

#### Floyd-Warshall
The [Floyd-Warshall algorithm](https://en.wikipedia.org/wiki/Floyd%E2%80%93Warshall_algorithm) 
is used to find *all* paths, between each possible start vertex and each 
possible end vertex.

With an *O(**v**^3)* runtime, **v** being the amount of vertices, the 
Floyd-Warshall algorithm is probably much too slow to be used at runtime.
Since the algorithm finds **all** possible shortest paths through the 
environment, however, it's exceptionally well-suited to build an index of 
shortest paths at deploy time.

For static environments, this means a significant reduction in path finding 
costs at runtime: with all shortest paths already known, there is no need to 
search for paths at runtime.

In environments that are likely to change only slightly, the result of the 
Floyd-Warshall algorithm may be used as a heuristic for the [A*](#a*) search.

### Flavours
#### Dynamic Pathfinder
The Dynamic Pathfinder is an automatically composing pathfinder that alternates 
between using the [A*](#a*) and [Dijkstra's](#dijkstra) algorithms based on the 
type of request and environment.

For finding multiple paths, it always uses Dijkstra's algorithm. In searching 
for a single path, A* is used when:
- the path traverses a [geographical environment](#environments), to take 
advantage of guidance by heuristics
- the graph contains edges with negative costs, to prevent infinite loops with 
A*'s [open/closed sets](https://en.wikipedia.org/wiki/Open_set)

Euclidean distance is used by default, a different one can be specified using, 
for example:
```php
DynamicPathfinder::withHeuristic(Chebyshev::dimensions(2), $environment);
```

When available, a map of the environment (the [Floyd-Warshall](#floyd-warshall) 
result) can be provided using:
```php
DynamicPathfinder::withHeuristic(FromPreviousEnvironment::state(
    $heuristic, 
    $environment
));
```
Since the environment is assumed to be dynamic, the map is only used as [A*](#a*)
heuristic.

#### Static Pathfinder
The Static Pathfinder assumes the environment to be unchanging, and possesses a 
map with the shortest paths through that environment.

While limited to unchanging environments, the static pathfinder is by far the 
fastest solution. Under the hood, all it does is a bunch of lookups, the amount 
of which exactly equals the length of the path.

### Graphs
Main article: [Graphs](Graphs.md).

The pathfinder works on [labeled](Graphs.md#labeled) [directional](Graphs.md#directed) 
graphs that contain [weights](Graphs.md#weighed).
Each vertex in the graph has at least one label, to serve as identification.

The cost of a path through the graph is determined by the sum of the weight of 
its edges.

#### Environments
When finding paths through [graphs with spatial properties](Graphs.md#environments), 
single-target search can be optimised by using [heuristics](https://en.wikipedia.org/wiki/Heuristic_(computer_science)).

The graphs as created in the [example section](#examples) of this document are 
examples of such environments.

Any number of [Euclidean spatial dimensions](https://en.wikipedia.org/wiki/Euclidean_space)
can be used, limited only by the configuration of the heuristic.

#### Networks
Not all graphs have associated geographic information. Those that do not are 
called [networks](Graphs.md#networks).

Although the pathfinder cannot make use of heuristics to speed up the search, 
it can still adequately find the cheapest path.
```php
GraphNetwork::create()
    ->withVertex('A', WithEdge::to('B', 5)->andTo('C', 8))
    ->withVertex('B', WithEdge::to('D', 9)->andTo('A', 1))
    ->withVertex('C', WithEdge::to('D', 4)->andTo('A', 1))
    ->withVertex('D', WithEdge::to('B', 3)->andTo('C', 9))
    ->make();
```
