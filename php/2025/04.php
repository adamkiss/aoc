<?php

use Adamkiss\Toolkit\A;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
..@@.@@@@.
@@@.@.@.@@
@@@@@.@.@@
@.@@@@..@.
@@.@@@@.@@
.@@@@@@@.@
.@.@.@.@@@
@.@@@.@@@@
.@@@@@@@@.
@.@.@@@.@.
INPUT;

/**
 * @param string $input
 * @return array<int, array<int,bool>>
 */
function parseinput(string $input): array {
	$lines = explode("\n", trim($input));
	return A::map($lines, function ($row) {
		$r = str_split($row);
		return A::map($r, fn ($ch) => ($ch === '@'));
	});
}

/**
 * Counts `true`s in 8 neighbours of an <x,y> position
 *
 * @param array<int, array<int,bool>> $grid
 * @param int $x
 * @param int $y
 * @return int
 */
function countneighbours(array $grid, int $x, int $y): int {
	$maxy = count($grid);
	$maxx = count($grid[0]);
	$n = 0;
	for ($dx = -1; $dx <= 1; $dx++) {
		for ($dy = -1; $dy <= 1; $dy++) {
			if ($dx === $dy && $dx === 0) {
				continue;
			}
			if ($x + $dx < 0 || $y + $dy < 0 || $x + $dx >= $maxx || $y + $dy >= $maxy) {
				continue;
			}
			if ($grid[$y + $dy][$x + $dx] === false) {
				continue;
			}
			$n++;
		}
	}
	return $n;
}

/**
 * Counts all accessible (has less than four "true" neighbours)
 *
 * @param array<int, array<int,bool>> $grid
 * @return int
 */
function countaccessible(array $grid): int {
	$accessible = 0;
	foreach ($grid as $y => $line) {
		foreach ($line as $x => $b) {
			if (!$b) {
				continue;
			}
			if (countneighbours($grid, $x, $y) < 4) {
				$accessible++;
			}
		}
	}
	return $accessible;
}

/**
 * @param array<int, array<int,bool>> $grid
 */
function part1(array $grid): int {
	return countaccessible($grid);
}

/**
 * @param array<int, array<int,bool>> $grid
 */
function part2(array $grid): int {
	$removed = 0;
	$removable = countaccessible($grid);
	while ($removable !== 0) {
		$tbr = [];
		foreach ($grid as $y => $line) {
			foreach ($line as $x => $b) {
				if (!$b) {
					continue;
				}
				if (countneighbours($grid, $x, $y) >= 4) {
					continue;
				}
				$tbr []= [$x, $y];
			}
		}
		foreach ($tbr as [$x, $y]) {
			$grid[$y][$x] = false;
			$removed++;
		}

		$removable = countaccessible($grid);
	}
	return $removed;
}

$s = microtime(true);

$gridd = parseinput($input_demo);
$gridi = parseinput($input);

println('0) Parsing: ');
printf("» %.3fms\n", (microtime(true) - $s) * 1000);

// 1
$p = microtime(true);
$r = part1($gridd);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r === 13);

$p = microtime(true);
println('1) Result of real input: ' . part1($gridi));
printf("» %.3fms\n", (microtime(true) - $p) * 1000);

// 2
$p = microtime(true);
$r = part2($gridd);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r === 43);

$p = microtime(true);
println('2) Result of real input: ' . part2($gridi));
printf("» %.3fms\n", (microtime(true) - $p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true) - $s) * 1000);
