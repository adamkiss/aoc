<?php

use Adamkiss\Toolkit\A;
use Adamkiss\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
11-22,95-115,998-1012,1188511880-1188511890,222220-222224,1698522-1698528,446443-446449,38593856-38593862,565653-565659,824824821-824824827,2121212118-2121212124
INPUT;

function part1 (string $input) {
	$ranges = A::map(Str::split($input, ','), function($r) {
		$r = Str::split($r, '-');
		return [(int)$r[0], (int)$r[1]];
	});
	$invalid = [];
	foreach ($ranges as [$r0, $r1]) {
		for ($i=$r0; $i <= $r1; $i++) {
			$id = (string) $i;
			$halves = str_split($id, ceil(strlen($id) / 2));
			if (count($halves) >= 2 && $halves[0] === $halves[1]) {
				$invalid [] = $i;
			}
		}
	}

	return array_sum($invalid);
}

function part2 (string $input) {
	$rep = [2, 3, 5, 7];
	$ranges = A::map(Str::split($input, ','), function($r) {
		$r = Str::split($r, '-');
		return [(int)$r[0], (int)$r[1]];
	});
	$invalid = [];
	foreach ($ranges as [$r0, $r1]) {
		for ($i=$r0; $i <= $r1; $i++) {
			$id = (string) $i;
			foreach ($rep as $rp) {
				$parts = str_split($id, ceil(strlen($id) / $rp));
				if (count($parts) < $rp) {
					continue;
				}
				$p1 = $parts[0];
				foreach ($parts as $p) {
					if ($p !== $p1) {
						continue 2;
					}
				}
				if (! in_array($i, $invalid)) {
					$invalid [] = $i;
				}
			}
		}
	}

	return array_sum($invalid);
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 1227775554);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 4174379265);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
