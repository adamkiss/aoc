<?php

use Adamkiss\Toolkit\A;
use Adamkiss\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
987654321111111
811111111111119
234234234234278
818181911112111
INPUT;
$input_demo1 = <<<INPUT
811111111111119
INPUT;

function part1(string $input) {
	$banks = Str::split($input, "\n");
	$banks = A::map($banks, fn ($b) => A::map(str_split($b), intval(...)));
	$outputs = [];
	foreach ($banks as $b) {
		$top = -1;
		foreach ($b as $cur => $bat) {
			for ($i = $cur + 1; $i < count($b); $i++) {
				$test = $bat * 10 + $b[$i];
				if ($test > $top) {
					$top = $test;
				}
			}
		}
		$outputs [] = $top;
	}
	return array_sum($outputs);
}

function digitstoint(array $digits = []): int {
	return (int)join('', $digits);
}

function largestindexinarray(array $numbers = []): int {
	$largest = -1;
	$largesti = -1;
	foreach ($numbers as $k => $v) {
		if ($v > $largest) {
			$largest = $v;
			$largesti = $k;
		}
	}
	return $largesti;
}

const P2SIZE = 12;
function part2(string $input) {
	$banks = Str::split($input, "\n");
	$banks = A::map($banks, fn ($b) => A::map(str_split($b), intval(...)));
	$outputs = [];
	foreach ($banks as $b) {
		$found = [];
		$last = 0;
		$len = count($b);
		$wsize = $len - P2SIZE + 1;
		while (count($found) < P2SIZE) {
			$search = array_slice($b, $last, $wsize);
			$next = largestindexinarray($search);
			if ($next !== 0) {
				$wsize -= $next;
			}
			$last = $next + $last;
			$found [] = $b[$last];
			$last++;
		}
		$outputs [] = digitstoint($found);
	}
	return array_sum($outputs);
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r === 357);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true) - $p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r === 3121910778619);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true) - $p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true) - $s) * 1000);
