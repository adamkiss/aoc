<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
3,4,3,1,2
INPUT;

function part1 (string $input) {
	$fishes = array_fill(0, 9, 0);
	foreach(explode(',', $input) as $state) { $fishes[$state]++; }

	for ($i=0; $i < 80; $i++) {
		$spawn = array_shift($fishes);
		$fishes[6] += $spawn;
		$fishes []= $spawn;
	}

	return array_sum($fishes);
}

function part2 (string $input) {
	$fishes = array_fill(0, 9, 0);
	foreach(explode(',', $input) as $state) { $fishes[$state]++; }

	for ($i=0; $i < 256; $i++) {
		$spawn = array_shift($fishes);
		$fishes[6] += $spawn;
		$fishes []= $spawn;
	}

	return array_sum($fishes);
}

$s = microtime(true);

// 1
$p = microtime(true);
println('1) Result of demo: ' . part1($input_demo));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
println('2) Result of demo: ' . part2($input_demo));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
