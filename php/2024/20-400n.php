<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input('20.txt');
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

function vtos(int $x, int $y) : string {
	return "{$x};{$y}";
}

function find_shortest_path(array $m, int $w, int $h, array $s, array $e) : array {
	$v = array_fill(0, $h, array_fill(0, $w, false));
	$q = new SplPriorityQueue();
	$q->insert([$s[0],$s[1],[],0], 0);

	$c = 0;
	while(!$q->isEmpty()) {
		[$x, $y, $path, $steps] = $q->extract();

		$path[vtos($x,$y)] = [$steps, $x, $y];
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

function part1 (array $p, int $above) {
	$pk = array_keys($p);
	$shortcuts = 0;
	$loops = 0;
	for ($i=0; $i < count($p); $i++) {
		[$b, $bx, $by] = $p[$pk[$i]];
		foreach ([[-2,0], [0,-2], [2, 0], [0, 2]] as [$ix, $iy]) {
			$loops++;
			$ax = $bx + $ix;
			$ay = $by + $iy;
			$axy = "{$ax};{$ay}";
			if (!isset($p[$axy])) {
				continue;
			}
			[$a] = $p[$axy];
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
	println(PHP_EOL . sprintf('↓ took %s iterations', number_format($loops)));
	return $shortcuts;
}

function part2 (array $p, int $above) {
	$shortcuts = 0;
	$loops = 0;

	$validdiffs = [];
	for ($i=-20; $i <= 20; $i++) {
		for ($j=-20; $j <= 20; $j++) {
			$ia = $i<0?-$i:$i;
			$ja = $j<0?-$j:$j;
			if ($ia+$ja > 20 || $ia+$ja === 0) {
				continue;
			}
			$validdiffs [] = [$i, $j, $ia+$ja];
		}
	}
	foreach ($p as [$b, $bx, $by]) {
		foreach ($validdiffs as [$dx, $dy, $len]) {
			$loops++;
			$a_ = vtos($bx + $dx, $by + $dy);
			if (!isset($p[$a_]) || $b < $p[$a_][0]) {
				continue;
			}
			if ($above > $b - $p[$a_][0] - $len) {
				continue;
			}
			$shortcuts++;
		}
	}
	println(PHP_EOL . sprintf('↓ took %s iterations', number_format($loops)));
	return $shortcuts;
}

$s = microtime(true);

[,$pathd] = find_shortest_path(...process_input($input_demo));
[,$path ] = find_shortest_path(...process_input($input));

// 1
$p = microtime(true);

$r = part1($pathd, 34);
println('1) DEMO (save 34+): ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 4);

$p = microtime(true);
println('1) Real (save 100+): ' . part1($path, 100));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($pathd, 50);
println('2) demo (50+): ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 285);

$p = microtime(true);
println('2) real input: ' . part2($path, 100));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
