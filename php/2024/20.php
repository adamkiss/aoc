<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
###############
#...#...#.....#
#.#.#.#.#.###.#
#S#...#.#.#...#
#######.#.#.###
#######.#.#...#
#######.#.###.#
###..E#...#...#
###.#######.###
#...###...#...#
#.#####.#.###.#
#.#...#.#.#...#
#.#.#.#.#.#.###
#...#...#...###
###############
INPUT;

function process_input(string $input) : array {
	$m = [];
	$s = null;
	$e = null;
	foreach (explode("\n", $input) as $y => $line) {
		$m[$y] = [];
		foreach (str_split($line) as $x => $chr) {
			$m[$y][$x] = $chr === '#' ? false : true;
			if ($chr === 'S') {
				$s = [$x, $y];
			}
			if ($chr === 'E') {
				$e = [$x, $y];
			}
		}
	}
	$w = count($m[0]);
	$h = count($m);
	return [$m, $w, $h, $s, $e];
}

function find_shortest_path(array $m, int $w, int $h, array $s, array $e) : array {
	$v = array_fill(0, $h, array_fill(0, $w, false));
	$q = new SplPriorityQueue();
	$q->insert([$s[0],$s[1],[],0], 0);

	$c = 0;
	while(!$q->isEmpty()) {
		[$x, $y, $path, $steps] = $q->extract();

		$path["$x;$y"] = [$steps, $x, $y];
		if ($x === $e[0] && $y === $e[1]) {
			return [$steps, $path];
		}
		$steps++;

		foreach ([[1, 0], [0, 1], [0, -1], [-1, 0]] as [$dx, $dy]) {
			$nx = $x + $dx;
			$ny = $y + $dy;

			if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) {
				continue;
			}
			if (!$m[$ny][$nx] || $v[$ny][$nx]){
				continue;
			}

			$q->insert([$nx, $ny, $path, $steps], -$steps);
			$v[$ny][$nx] = true;
		}
	}

	return [null, $c];
}

function part1_sp(string $input) {
	[$m, $w, $h, $s, $e] = process_input($input);
	$best = find_shortest_path($m, $w, $h, $s, $e);
	return $best[0];
}

function part1 (string $input, int $above) {
	[$m, $w, $h, $s, $e] = process_input($input);
	[$_, $path] = find_shortest_path($m, $w, $h, $s, $e);
	$pk = array_keys($path);
	$shortcuts = 0;
	$loops = 0;
	for ($i=0; $i < count($path); $i++) {
		[$b, $bx, $by] = $path[$pk[$i]];
		foreach ([[-2,0], [0,-2], [2, 0], [0, 2]] as [$ix, $iy]) {
			$loops++;
			$ax = $bx + $ix;
			$ay = $by + $iy;
			$axy = "{$ax};{$ay}";
			if (!isset($path[$axy])) {
				continue;
			}
			[$a] = $path[$axy];
			$saved = $a - $b - 2;
			if (isset($shortcuts_debug[$saved])) {
				$shortcuts_debug[$saved]++;
			} else {
				$shortcuts_debug[$saved] = 1;
			}
			if ($saved < $above) {
				continue;
			}
			$shortcuts++;
		}
	}
	println("↓ took {$loops} iterations");
	return $shortcuts;
}

function part2 (string $input, int $above) {
	[$m, $w, $h, $s, $e] = process_input($input);
	[, $path] = find_shortest_path($m, $w, $h, $s, $e);
	$path = array_values($path);

	$shortcuts = 0;
	$loops = 0;
	$i = 0;
	foreach (array_slice($path, 0, count($path) - $above) as $i => $b) {
		foreach (array_slice($path, $i+$above+2) as $a) {
			$loops++;
			$l = abs($b[1] - $a[1]) + abs($b[2] - $a[2]);
			if ($l > 20 || ($a[0]-$b[0]-$l) < $above) {
				continue;
			}
			$shortcuts++;
		}
	}
	println("↓ took {$loops} iterations");
	return $shortcuts;
}

$s = microtime(true);

// 1
$p = microtime(true);

$r = part1_sp($input_demo);
println('1) DEMO Shortest path: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 84);

$r = part1($input_demo, 34);
println('1) DEMO (save 34+): ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 4);

$p = microtime(true);
println('1) Real (save 100+): ' . part1($input, 100));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo, 50);
println('2) demo (50+): ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 285);

$p = microtime(true);
println('2) real input: ' . part2($input, 100));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
