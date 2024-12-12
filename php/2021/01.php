<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = explode("\n", read_input());
$input_demo = [199, 200, 208, 210, 200, 207, 240, 269, 260, 263];

function part1 (array $input) {
	return array_reduce($input, function(int $total, int $item) use (&$prev) {
		if (isset($prev)) {
			if ($item > $prev) { $total += 1; }
		}
		$prev = $item;
		return $total;
	}, 0);
}

function part2 (array $input) {
	// Create windows of [i, i+1, i+2] from the input
	// and just run part 1 on that window instead of the original input
	$windows = [];
	for ($i=0; $i < count($input)-2; $i++) {
		$windows []= array_sum(array_slice($input, $i, 3));
	}
	return part1($windows);
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
