# Graphs
In order to find a path, one needs an environment to search in. Such environment 
is known as a [*graph*](https://en.wikipedia.org/wiki/Graph_theory).

This pathfinder module supports two major kinds of graphs: those that have 
[geometric information](#environments) associated to their nodes, and [those 
that do not](#networks).

## Properties
All supported graphs conform to the following properties:

### Directed
The pathfinder works on [directed graphs](https://en.wikipedia.org/wiki/Directed_graph),
for the simple reason that any undirected graph can be represented as directed 
graph by duplicating all edges and inverting the duplicates - therefore, 
supporting directed graphs implicitly supports both.

### Weighed
Each edge in a pathfinder graph has a *cost* (or *weight*) associated to it, 
making the graphs [weighed graphs](https://en.wikipedia.org/wiki/Glossary_of_graph_theory_terms#weighted_graph).
The cost of a path through the graph is determined by the sum of the weight of 
its edges.

The individual weights are used by the pathfinding algorithms to determine the 
cheapest / shortest routes.

The most common use case of the pathfinder is to find the shortest possible path.
In such cases, the weight of the edge should be equal to its length.

It is also possible to use the pathfinder to find optimal paths that may not be 
the shortest. Extra weight, for instance, may be given to exceptionally 
difficult terrain, dangerous areas or other unwanted sections.

In the context of a [grid builder](#grid-building), one attaches the weights to 
the cells rather than to the connections between them. When building the graph, 
the builder applies those costs to the edges flowing towards the cell.

### Labeled
Each vertex in the graph has at least one label, to serve as identification.
This identification label is crucial, not least because the paths as found by 
the pathfinders consists of a list of these labels.

As such, all supported graphs are always [labeled graphs](https://en.wikipedia.org/wiki/Graph_labeling).

Some graphs, notably [geographic environments](#environments), have additional 
labels associated to their vertices, in order to define their geometric location.

## Types
The pathfinder can operate on both geographical graphs and their non-geometric
counterparts.

### Environments
In the context of the pathfinder module, **Environments** are graphs with edges 
that have [Cartesian coordinates](https://en.wikipedia.org/wiki/Cartesian_coordinate_system) 
associated to them.

Having this geometric information allows for an advantage in finding the 
shortest path between nodes, through the use of a heuristic. Heuristics enable 
the [A* pathfinder](README.md#a*) to focus on the most likely candidates first, 
potentially skipping many uninteresting candidates amd thus finding the optimal 
path faster.

### Networks
Graphs without geographic information are referred to as **Networks**.

Due to the absence of locational data, heuristics do not apply. This takes away 
the performance benefits of the A* search algorithm, but does not make it 
impossible to find the shortest paths through the network.

Since heuristics cannot offer any benefits in this scenario, it is advisable to 
use [Dijkstra's algorithm](README.md#dijkstra) instead. A* search can also be 
used, for example to avoid potential negative [cycles](https://en.wikipedia.org/wiki/Cycle_(graph_theory)).
In order to use A* on a network, the network must first be converted to an 
environment.

Conversion from a network to an environment can be achieved using:
```php
GeometricView::of($theNetwork)
```
The resulting environment will consider each node to be located at position 0.

## Creation
Several tools are available to aid in the construction of the graphs in which 
the paths are to be found.

### Grid building
When constructing an environment in the shape of a grid (formally known as a 
[lattice graph](https://en.wikipedia.org/wiki/Lattice_graph)) it may be 
burdensome to specify the geometric location of each vertex.

The **grid environment** builder offers an alternative, by exposing methods to 
define the rows and columns, filling in the geometric details automatically.

Rather than dealing with vertices and edges directly, the grid environment deals 
with *squares* and *obstacles*. 

Grids can be defined through methods:
```php
<?php
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;
use Stratadox\Pathfinder\Graph\Builder\Obstacle;
use Stratadox\Pathfinder\Graph\Builder\Square;

$environment = GridEnvironment::create()
    ->withRow(
        Square::labeled('A1')->costing(2),
        Square::labeled('B1'),
        Square::labeled('C1'),
        Square::labeled('D1')
    )
    ->withRow(
        Square::labeled('A2')->costing(10),
        Obstacle::here(),
        Obstacle::here(),
        Square::labeled('D2')
    )
    ->withRow(
        Square::labeled('A3')->costing(2),
        Square::labeled('B3'),
        Square::labeled('C3'),
        Square::labeled('D3')
    )
    ->make();
```
...or by passing in a 2D array, allowing for a more concise and visual syntax:
```php
<?php
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;

$environment = GridEnvironment::fromArray([
    [ 2.0, 1.0, 1.0, 1.0],
    [10.0, INF, INF, 1.0],
    [ 2.0, 1.0, 1.0, 1.0],
])->make();
```
When constructing a grid from arrays, the identifying label of the vertices are 
generated by using one or more letters for the column index and a number for 
the row. The resulting identifiers correspond to a notation as used in i.e. 
chess or excel.

By default, horizontal and vertical edges are generated. In order to also allow 
diagonal movement, one can use `diagonalMovementAllowed`:
```php
<?php
use Stratadox\Pathfinder\Graph\Builder\GridEnvironment;

$environment = GridEnvironment::fromArray([
    [ 2.0, 1.0, 1.0, 1.0],
    [10.0, INF, INF, 1.0],
    [ 2.0, 1.0, 1.0, 1.0],
])->diagonalMovementAllowed()->make();
```

### Graph building
Not all environments are shaped like a grid: those that are not can be 
constructed using the **graph environment** builder.

The graph environment builder aids the construction of graphs, allowing for more 
freedom than its [grid-based counterpart](#grid-building).

```php
<?php
use Stratadox\Pathfinder\Graph\At;
use Stratadox\Pathfinder\Graph\Builder\GraphEnvironment;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;

$environment =  GraphEnvironment::create()
    ->withLocation('A', At::position(0, 0), WithEdge::to('B', 5)->andTo('C', 8))
    ->withLocation('B', At::position(0, 5), WithEdge::to('A', 5)->andTo('D', 4))
    ->withLocation('C', At::position(8, 0), WithEdge::to('A', 8)->andTo('D', 5))
    ->withLocation('D', At::position(4, 4), WithEdge::to('B', 3)->andTo('C', 9))
    ->make();
```
For environments where the cost of each edge equals the length of said edge, it 
may be preferred to automatically assign weights to the edges:

```php
<?php
use Stratadox\Pathfinder\Distance\Euclidean;
use Stratadox\Pathfinder\Graph\At;
use Stratadox\Pathfinder\Graph\Builder\GraphEnvironment;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;

$environment =  GraphEnvironment::create()
    ->withLocation('A', At::position(0, 0), WithEdge::to('B')->andTo('C'))
    ->withLocation('B', At::position(0, 5), WithEdge::to('A')->andTo('D'))
    ->withLocation('C', At::position(8, 0), WithEdge::to('A')->andTo('D'))
    ->withLocation('D', At::position(4, 4), WithEdge::to('B')->andTo('C'))
    ->determineEdgeCostsAs(Euclidean::distance())
    ->make();
```
When automatically determining the costs of edges that already have a cost 
associated, the default cost (1) is subtracted from the existing cost and the 
distance is added.

For example, when the edge from A to B has a defined cost of 5 and a length of 
12.4, after auto-determination the edge will cost 5 - 1 + 12.4 = 16.4.

### Network building
Both previously described builders assume the graph to have geometric properties.
When building a [network](#networks), no geographical information is required.

Building a network looks a lot like building a spatial graph, the difference 
being a lack of spatial information:

```php
<?php
use Stratadox\Pathfinder\Graph\Builder\GraphNetwork;
use Stratadox\Pathfinder\Graph\Builder\WithEdge;

GraphNetwork::create()
    ->withVertex('A', WithEdge::to('B', 5)->andTo('C', 8))
    ->withVertex('B', WithEdge::to('D', 9)->andTo('A', 1))
    ->withVertex('C', WithEdge::to('D', 4))
    ->withVertex('D', WithEdge::to('B', 3)->andTo('C', 9))
    ->make();
```
