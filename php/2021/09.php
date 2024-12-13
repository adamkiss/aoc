<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
2199943210
3987894921
9856789892
8767896789
9899965678
INPUT;

const DIRECTIONS = [
	[-1, 0], [1, 0], [0, -1], [0, 1]
];

function to_map(string $i) {
	return array_map(
		fn($l) => array_map('intval', str_split($l)),
		explode("\n", $i)
	);
}

function vec_add(int $x, int $y, int $vx, int $vy): array
{
	return [$x + $vx, $y + $vy];
}

function is_in_bounds(int $x, int $y, int $mx, int $my): bool {
	return $x >= 0 && $x < $mx && $y >= 0 && $y < $my;
}

function is_lowest_of_neighbours(array $m, int $x, int $y, int $mx, int $my): bool {
	$self = $m[$y][$x];
	foreach (DIRECTIONS as $v) {
		[$dx, $dy] = vec_add($x, $y, ...$v);
		if (!is_in_bounds($dx, $dy, $mx, $my)) {
			continue;
		}
		if ($m[$dy][$dx] <= $self) {
			return false;
		}
	}
	return true;
}

function part1 (string $input) {
	$m = to_map($input);
	$mx = count($m[0]);
	$my = count($m);
	$l = [];
	foreach ($m as $y => $line) {
		foreach ($line as $x => $height) {
			if (! is_lowest_of_neighbours($m, $x, $y, $mx, $my)) {
				continue;
			}
			$l [] = $height + 1;
		}
	}
	return array_sum($l);
}

function flood_fill($m, &$ch, $x, $y, $mx, $my): int {
	$ch[$y][$x] = true;
	$size = 1;
	foreach (DIRECTIONS as $v) {
		[$dx, $dy] = vec_add($x, $y, ...$v);
		if (!is_in_bounds($dx, $dy, $mx, $my)) {
			continue;
		}
		if ($ch[$dy][$dx]) {
			continue;
		}
		if ($m[$dy][$dx] === 9) {
			continue;
		}

		$size += flood_fill($m, $ch, $dx, $dy, $mx, $my);
	}
	return $size;
}

function part2 (string $input) {
	$m = to_map($input);
	$mx = count($m[0]);
	$my = count($m);
	$ch = array_fill(0, $my, array_fill(0, $mx, false));
	$l = [];
	foreach ($m as $y => $line) {
		foreach ($line as $x => $height) {
			if (! is_lowest_of_neighbours($m, $x, $y, $mx, $my)) {
				continue;
			}
			$l [] = [$height + 1, flood_fill($m, $ch, $x, $y, $mx, $my)];
		}
	}
	usort($l, fn ($a, $b) => $b[1] <=> $a[1]);
	return $l[0][1] * $l[1][1] * $l[2][1];
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 15);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 1134);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
