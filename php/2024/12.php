<?php

use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$di = <<<INPUT
RRRRIICCFF
RRRRIICCCF
VVRRRCCFFF
VVRCCCJFFF
VVVVCJJCFE
VVIVCCJJEE
VVIIICJJEE
MIIIIIJJEE
MIIISIJEEE
MMMISSJEEE
INPUT;
$di21 = <<<INPUT
AAAA
BBCD
BBCC
EEEC
INPUT;
$di22 = <<<INPUT
EEEEE
EXXXX
EEEEE
EXXXX
EEEEE
INPUT;
$di23 = <<<INPUT
AAAAAA
AAABBA
AAABBA
ABBAAA
ABBAAA
AAAAAA
INPUT;

const DEBUG = false;
const DIRS = [
	[1, 0], [-1, 0],
	[0, 1], [0, -1]
];

function to_map(string $input): array {
	$map = array_map(
		fn ($l) => array_map(
			fn($ch) => ['id' => $ch, 'plot' => false, 'edges' => false, 'corners' => 0],
			str_split($l)
		),
		explode(string: $input, separator: "\n")
	);
	return $map;
}
function in_bounds(int $x, int $y, int $Xmax, int $Ymax): bool {
	return $x >= 0 && $x < $Xmax && $y >= 0 && $y < $Ymax;
}
function count_edges(int $x, int $y, int $w, int $h): int
{
	return (
		($x === 0 || $x === $w-1 ? 1 : 0)
		+ ($y === 0 || $y === $h-1 ? 1 : 0)
	);
}
function plot_corners(array $map) : void {
	foreach ($map as $y => $line) {
		foreach ($line as $x => $v) {
			print($v['id'] === 'C' ? $v['corners'] : '.');
		}
		println();
	}
}

function vec_sub(int $x1, int $y1, int $x2, int $y2): array {
	return [$x1 - $x2, $y1 - $y2];
}
function vec_add(int $x1, int $y1, int $x2, int $y2): array {
	return [$x1 + $x2, $y1 + $y2];
}

function map_plot(&$map, $x, $y, $w, $h, &$plots, $pid): void {
	$id = $map[$y][$x]['id'];
	foreach (DIRS as [$dx, $dy]) {
		[$nx, $ny] = vec_add($x, $y, $dx, $dy);
		if (!in_bounds($nx, $ny, $w, $h)) {
			$plots[$pid]['perimeter'] += 1;
			continue;
		}
		if ($map[$ny][$nx]['id'] !== $id) {
			$plots[$pid]['perimeter'] += 1;
			continue;
		}
		if ($map[$ny][$nx]['plot'] !== false){
			continue;
		}

		$map[$ny][$nx]['plot'] = $pid;
		// $map[$ny][$nx]['edges'] = count_edges($nx, $ny, $w, $h);
		$plots[$pid]['size'] += 1;
		// $plots[$pid]['perimeter'] += count_edges($nx, $ny, $w, $h);
		$plots[$pid]['cells'] [] = [$nx, $ny];
		map_plot($map, $nx, $ny, $w, $h, $plots, $pid);
	}
}

function part1 (string $input) {
	$map = to_map($input);

	$w = count($map[0]);
	$h = count($map);

	$plots = [];

	for ($y=0; $y < $h; $y++) {
		for ($x=0; $x < $w; $x++) {
			if ($map[$y][$x]['plot'] !== false) {
				continue;
			}

			$plots []= [
				'id' => $map[$y][$x]['id'],
				'size' => 1,
				'perimeter' => 0,
				// 'cells' => [[$x, $y]],
			];
			$pid = count($plots) - 1;
			$map[$y][$x]['plot'] = $pid;
			map_plot($map, $x, $y, $w, $h, $plots, $pid);
		}
	}

	// rd($map);

	return array_reduce(
		$plots,
		function($c, $i) {
			return $c + $i['size'] * $i['perimeter'];
		},
		0
	);
}

// REGEX: self, left from diagonal, diagonal, right from diagonal
// self is the backreference, diagonal is the only different
const CONV_CORNER_REGEX = '/([A-Z])\1(?:(?!\1).)\1/';
function find_concave_corners($map, $x, $y) : array {
	$ids = array_map(
		fn($v) => $map[$y+$v[1]][$x+$v[0]]['id'] ?? ' ',
		[
			[-1,-1], [0,-1], [1,-1],
			[-1, 0], [0, 0], [1, 0],
			[-1, 1], [0, 1], [1, 1]
		]
	);
	$c = array_filter([
		// Str::matches(join('', [$ids[4], $ids[3], $ids[0], $ids[1]]), CONV_CORNER_REGEX)
		// 	? sprintf('%s;%s', $x, $y) : null,
		// Str::matches(join('', [$ids[4], $ids[1], $ids[2], $ids[5]]), CONV_CORNER_REGEX)
		// 	? sprintf('%s;%s', $x+1, $y) : null,
		// Str::matches(join('', [$ids[4], $ids[5], $ids[8], $ids[7]]), CONV_CORNER_REGEX)
		// 	? sprintf('%s;%s', $x+1, $y+1) : null,
		// Str::matches(join('', [$ids[4], $ids[7], $ids[6], $ids[3]]), CONV_CORNER_REGEX)
		// 	? sprintf('%s;%s', $x, $y+1) : null,
		($ids[4] === $ids[3] && $ids[4] !== $ids[0] && $ids[4] === $ids[1])
			? sprintf('%s;%s', $x, $y) : null,
		($ids[4] === $ids[1] && $ids[4] !== $ids[2] && $ids[4] === $ids[5])
			? sprintf('%s;%s', $x+1, $y) : null,
		($ids[4] === $ids[5] && $ids[4] !== $ids[8] && $ids[4] === $ids[7])
			? sprintf('%s;%s', $x+1, $y+1) : null,
		($ids[4] === $ids[7] && $ids[4] !== $ids[6] && $ids[4] === $ids[3])
			? sprintf('%s;%s', $x, $y+1) : null,
	], fn($b) => isset($b));
	return $c;
}

function map_plot2(&$map, $x, $y, $w, $h, &$plots, $pid): void {
	$id = $map[$y][$x]['id'];
	$e = '';
	foreach (DIRS as [$dx, $dy]) {
		[$nx, $ny] = vec_add($x, $y, $dx, $dy);
		if (!in_bounds($nx, $ny, $w, $h)) {
			$e .= ($dx === 0) ? 'h' : 'v';
			continue;
		}
		if ($map[$ny][$nx]['id'] !== $id) {
			$e .= ($dx === 0) ? 'h' : 'v';
			continue;
		}
		if ($map[$ny][$nx]['plot'] !== false){
			continue;
		}

		$map[$ny][$nx]['plot'] = $pid;
		$plots[$pid]['size'] += 1;
		map_plot2($map, $nx, $ny, $w, $h, $plots, $pid);
	}

	$c = match(strlen($e)) {
		2 => ($e === 'vv' || $e === 'hh') ? 0 : 1,
		3 => 2,
		4 => 4,
		default => 0
	};

	$map[$y][$x]['corners'] = $c;
	$plots[$pid]['corners'] += $c;
	if ($c < 2) {
		$plots[$pid]['concave'] = array_merge($plots[$pid]['concave'], find_concave_corners($map, $x, $y));
	}
}

function part2 (string $input) {
	$map = to_map($input);

	$w = count($map[0]);
	$h = count($map);

	$plots = [];

	for ($y=0; $y < $h; $y++) {
		for ($x=0; $x < $w; $x++) {
			if ($map[$y][$x]['plot'] !== false) {
				continue;
			}

			$plots []= [
				'id' => $map[$y][$x]['id'],
				'size' => 1,
				'corners' => 0,
				'concave' => []
			];
			$pid = count($plots) - 1;
			$map[$y][$x]['plot'] = $pid;
			map_plot2($map, $x, $y, $w, $h, $plots, $pid);
		}
	}

	if (DEBUG) {
		ray($plots);
	}

	return array_reduce(
		$plots,
		function($c, $i) {
			$totalcorners = $i['corners'] + count(array_unique($i['concave']));
			if (DEBUG) {
				printf('%s => %s * %s = %s', $i['id'], $i['size'], $totalcorners, $totalcorners * $i['size']);
				println();
			}
			return $c + $i['size'] * $totalcorners;
		},
		0
	);
}

$s = microtime(true);

// 1
$p = microtime(true);
println('1) Result of demo: ' . part1($di));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($di);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 1206);

$p = microtime(true);
$r = part2($di21);
println('2) Result of demo 2-1: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 80);

$p = microtime(true);
$r = part2($di22);
println('2) Result of demo 2-2: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 236);

$p = microtime(true);
$r = part2($di23);
println('2) Result of demo 2-3: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 368);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
