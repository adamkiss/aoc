<?php

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
............
........0...
.....0......
.......0....
....0.......
......A.....
............
............
........A...
.........A..
............
............
INPUT;

function to_map(string $input): array {
	return A::map(
		Str::split($input, "\n"),
		fn($line) => str_split($line)
	);
}

function printMap(array $map) {
	ray()->html(
		'<pre>'.
		A::join(A::map($map, fn($line) => A::join($line, '')), "\n")
		.'</pre>'
	);
}

function vec_sub(array $v1, array $v2): array {
	return [$v1[0] - $v2[0], $v1[1] - $v2[1]];
}
function vec_add(array $v1, array $v2): array {
	return [$v1[0] + $v2[0], $v1[1] + $v2[1]];
}
function in_bounds(array $v, int $Xmax, int $Ymax): bool {
	return $v[0] >= 0 && $v[0] < $Xmax && $v[1] >= 0 && $v[1] < $Ymax;
}

function part1 (string $input) {
	$map = to_map($input);
	$Xmax = count($map[0]);
	$Ymax = count($map);
	$symbols = [];
	$antis = [];

	for ($y=0; $y < $Ymax; $y++) {
		$line = $map[$y];
		for ($x=0; $x < $Xmax; $x++) {
			if ($line[$x] === '.' || $line[$x] === '#') {
				continue;
			}

			if (!isset($symbols[$line[$x]])) {
				$symbols[$line[$x]] []= [$x, $y];
				continue;
			}

			foreach ($symbols[$line[$x]] as $antenna) {
				$diff = [$x - $antenna[0], $y - $antenna[1]];

				$back = vec_sub($antenna, $diff);
				if (in_bounds($back, $Xmax, $Ymax)) {
					$key = "{$back[0]},{$back[1]}";
					isset($antis[$key]) || $antis[$key] = true;
					$back = vec_sub($back, $diff);
				}

				$forward = vec_add([$x, $y], $diff);
				if (in_bounds($forward, $Xmax, $Ymax)) {
					$key = "{$forward[0]},{$forward[1]}";
					isset($antis[$key]) || $antis[$key] = true;
					$forward = vec_add($forward, $diff);
				};
			}

			$symbols[$line[$x]] []= [$x, $y];
		}
	}

	return count($antis);
}

function part2 (string $input) {
	$map = to_map($input);
	$Xmax = count($map[0]);
	$Ymax = count($map);
	$symbols = [];
	$antis = [];

	for ($y=0; $y < $Ymax; $y++) {
		$line = $map[$y];
		for ($x=0; $x < $Xmax; $x++) {
			if ($line[$x] === '.' || $line[$x] === '#') {
				continue;
			}

			$symbol = $line[$x];
			$key = "{$x},{$y}";
			isset($antis[$key]) || $antis[$key] = true;

			if (!isset($symbols[$symbol])) {
				$symbols[$symbol] []= [$x, $y];
				continue;
			}

			foreach ($symbols[$symbol] as $antenna) {
				$diff = [$x - $antenna[0], $y - $antenna[1]];

				$back = vec_sub($antenna, $diff);
				while (in_bounds($back, $Xmax, $Ymax)) {
					$key = "{$back[0]},{$back[1]}";
					isset($antis[$key]) || $antis[$key] = true;
					$back = vec_sub($back, $diff);
				}

				$forward = vec_add([$x, $y], $diff);
				while (in_bounds($forward, $Xmax, $Ymax)) {
					$key = "{$forward[0]},{$forward[1]}";
					isset($antis[$key]) || $antis[$key] = true;
					$forward = vec_add($forward, $diff);
				};
			}

			$symbols[$symbol] []= [$x, $y];
		}
	}

	return count($antis);
}

// PART 1
// println('1) Result of demo: ' . part1($demoinput));
// println('1) Result of real input: ' . part1($input));
// println('–––');
// PART 2
// println('2) Result of demo: ' . part2($demoinput));
// println('2) Result of real input: ' . part2($input));
$s = microtime(true);
echo part1($input) . "\n";
echo part2($input) . "\n";
printf("TOTAL: %.3fms\n", (microtime(true) - $s) * 1000);
