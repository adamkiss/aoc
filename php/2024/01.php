<?php

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
3   4
4   3
2   5
1   3
3   9
3   3
INPUT;

function part1(string $input) {
	$left = [];
	$right = [];
	$diff = [];
	foreach (Str::split($input, "\n") as $line) {
		[$l, $r] = A::map(preg_split('/\s+/', $line), fn ($n) => intval($n));
		$left [] = $l;
		$right [] = $r;
	}
	sort($left);
	sort($right);
	foreach ($left as $i => $l) {
		$diff [] = abs($l - $right[$i]);
	}

	return array_sum($diff);
}

function part2(string $input) {
	$left = [];
	$right = [];
	$sum = [];
	foreach (Str::split($input, "\n") as $line) {
		[$l, $r] = A::map(preg_split('/\s+/', $line), fn ($n) => intval($n));
		$left [] = $l;
		if (array_key_exists($r, $right)) {
			$right[$r]++;
		} else {
			$right[$r] = 1;
		}
	}
	foreach ($left as $i => $l) {
		$sum [] = array_key_exists($l, $right) ? $l * $right[$l] : 0;
	}

	return array_sum($sum);
}

// PART 1
println('1) Result of demo: ' . part1($input_demo));
println('1) Result of real input: ' . part1($input));
println('–––');
// PART 2
println('2) Result of demo: ' . part2($input_demo));
println('2) Result of real input: ' . part2($input));
