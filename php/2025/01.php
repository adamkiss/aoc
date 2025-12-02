<?php

use Adamkiss\Toolkit\A;
use Adamkiss\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
L68
L30
R48
L5
R60
L55
L1
L99
R14
L82
INPUT;

function part1(string $input) {
	$pos = 50;
	$zero = 0;
	foreach (Str::split($input, "\n") as $l) {
		$dir = $l[0] === 'L' ? -1 : 1;
		$amt = (int)ltrim($l, 'LR');
		$pos += ($dir * $amt);
		while ($pos < 0) {
			$pos += 100;
		}
		while ($pos > 99) {
			$pos -= 100;
		}
		if ($pos === 0) {
			$zero++;
		}
	}
	return $zero;
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
// $p = microtime(true);
// $r = part2($input_demo);
// println('2) Result of demo: ' . $r);
// printf("» %.3fms\n", (microtime(true)-$p) * 1000);
// assert($r === 1);
//
// $p = microtime(true);
// println('2) Result of real input: ' . part2($input));
// printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
