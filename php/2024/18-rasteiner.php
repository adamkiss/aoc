<?php

require_once __DIR__ . '/vendor/autoload.php';

class Tile {
	const EMPTY = 0;
	const BLOCKED = 1;
}

class Grid {
	public $grid;
	public $width;
	public $height;

	public function __construct($grid, $width, $height) {
		$this->grid = $grid;
		$this->width = $width;
		$this->height = $height;
	}
}

function make_coords_iter($input) {
	$lines = explode("\n", trim($input));
	$coords = [];
	foreach ($lines as $line) {
		list($x, $y) = explode(",", $line);
		$coords[] = [(int)$x, (int)$y];
	}
	return $coords;
}

function make_grid($input, $width, $height, $bytes) {
	$tiles = array_fill(0, $height, array_fill(0, $width, Tile::EMPTY));
	$coords = make_coords_iter($input);

	for ($i = 0; $i < $bytes; $i++) {
		if (isset($coords[$i])) {
			list($x, $y) = $coords[$i];
			$tiles[$y][$x] = Tile::BLOCKED;
		}
	}

	return new Grid($tiles, $width, $height);
}

function find_path_cost($grid, &$count) {
	$width = $grid->width;
	$height = $grid->height;

	$start = [0, 0];
	$end = [$width - 1, $height - 1];

	$stack = new SplQueue();
	$stack->enqueue($start);

	$costs = array_fill(0, $height, array_fill(0, $width, PHP_INT_MAX));
	$costs[$start[1]][$start[0]] = 0;

	while (!$stack->isEmpty()) {
		list($x, $y) = $stack->dequeue();
		$cost = $costs[$y][$x];
		$count++;
		if ($x == $end[0] && $y == $end[1]) {
			return $cost;
		}

		foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as $dir) {
			$nx = $x + $dir[0];
			$ny = $y + $dir[1];
			if ($nx >= 0 && $nx < $width && $ny >= 0 && $ny < $height) {
				if ($grid->grid[$ny][$nx] == Tile::EMPTY && $cost + 1 < $costs[$ny][$nx]) {
					$costs[$ny][$nx] = $cost + 1;
					$stack->enqueue([$nx, $ny]);
				}
			}
		}
	}

	return null;
}

function solve_part1($input, $width, $height, $bytes) {
	$grid = make_grid($input, $width, $height, $bytes);
	$count = 0;

	$result = find_path_cost($grid, $count);
	// echo "Found solution after $count iterations\n";
	return $result;
}

function solve_part2($input, $width, $height) {
	$tiles = array_fill(0, $height, array_fill(0, $width, Tile::EMPTY));
	$coords = make_coords_iter($input);

	$map = new Grid($tiles, $width, $height);
	$count = 0;

	foreach ($coords as $coord) {
		list($x, $y) = $coord;
		$map->grid[$y][$x] = Tile::BLOCKED;

		if (find_path_cost($map, $count) === null) {
			// echo "Found solution after $count iterations\n";
			return "$x,$y";
		}
	}

	throw new Exception("No solution found");
}

function part1($input) {
	return solve_part1($input, 71, 71, 1024);
}

function part2($input) {
	return solve_part2($input, 71, 71);
}

// Test cases
$test_input = <<<EOT
5,4
4,2
4,5
3,0
2,1
6,3
2,4
1,5
0,6
3,3
2,6
5,1
1,2
5,5
2,5
6,5
1,4
0,4
6,4
1,1
6,1
1,0
0,5
1,6
2,0
EOT;

$test_result_1 = 22;
$test_result_2 = "6,1";

$s = microtime(true);

// Test for part1
$p = microtime(true);
assert(solve_part1($test_input, 7, 7, 12) === $test_result_1);
printf("D1 » %.3fms\n", (microtime(true)-$p) * 1000);

// Test for part2
$p = microtime(true);
assert(solve_part2($test_input, 7, 7) === $test_result_2);
printf("D2 » %.3fms\n", (microtime(true)-$p) * 1000);

$inp = read_input('18.txt');

$p = microtime(true);
println(part1($inp));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println(part2($inp));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);


printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
