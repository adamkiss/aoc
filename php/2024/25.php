<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
#####
.####
.####
.####
.#.#.
.#...
.....

#####
##.##
.#.##
...##
...#.
...#.
.....

.....
#....
#....
#...#
#.#.#
#.###
#####

.....
.....
#.#..
###..
###.#
###.#
#####

.....
.....
.....
#....
#.#..
#.#.#
#####
INPUT;

function process_input(string $input) : array {
	$l = [];
	$k = [];

	foreach (explode("\n\n", $input) as $korl) {
		$iskey = $korl[0] === '.';
		$t = array_fill(0,5,0);
		$j = 0;
		for ($i=($iskey ? 0 : 6); $i < ($iskey ? strlen($korl)-6 : strlen($korl)); $i++) {
			switch ($korl[$i]) {
				case "\n":
					$j = 0;
					continue 2;
				case ".":
					$j++;
					continue 2;
				default:
					$t[$j++] += 1;
			}
		}
		if ($iskey) {
			$k [] = $t;
		} else {
			$l [] = $t;
		}
	}

	return [$l, $k];
}

function part1 (string $input) {
	[$l, $k] = process_input($input);
	$fit = 0;
	foreach ($l as $lock) {
		foreach ($k as $key) {
			for ($i=0; $i < 5; $i++) {
				if ($lock[$i] + $key[$i] > 5) {
					continue 2;
				}
			}
			$fit++;
		}
	}
	return $fit;
}

function part2 (string $input) {
	return true;
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 3);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 1);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
