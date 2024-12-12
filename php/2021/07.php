<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
16,1,2,0,4,2,7,1,2,14
INPUT;

function part1 (string $input) {
	$input = explode(',', $input);
	sort($input);
	$center = round(array_sum(array_slice($input, round(count($input)/2) - 1, 2)) / 2);
	return array_reduce(
		$input,
		fn($distance, $position) => $distance + abs($position - $center),
		0
	);
}

function summation(int $i): int
{
	return $i * ($i + 1) / 2;
}

function part2 (string $input) {
	$input = explode(',', $input);
	$avg = array_sum($input) / count($input);

	$avgs = array_reduce(
		$input,
		fn($avgs, $pos) => [
			$avgs[0] + summation(abs($pos - floor($avg))),
			$avgs[1] + summation(abs($pos - ceil($avg)))
		],
		[0,0]
	);

	return min(...$avgs);
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
