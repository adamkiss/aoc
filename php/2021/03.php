<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
00100
11110
10110
10111
10101
01111
00111
11100
10000
11001
00010
01010
INPUT;

function strToBits(string $str): array
{
	return array_map(fn($chr) => $chr === '1', str_split($str));
}

function mostCommon(array $arr, int $position) {
	$bits = array_column($arr, $position);
	return count(array_filter($bits, fn($i) => $i)) >= count($bits) / 2;
}

function bitsToDec(array $bits): int
{
	return bindec(implode('', array_map(fn($bit) => $bit ? '1' : '0', $bits)));
}

function part1 (string $input) {
	$bits = array_map(fn($line) => strToBits($line), explode("\n", $input));
	$epsilon = array_map(fn($pos) => mostCommon($bits, $pos), array_keys($bits[0]));
	$gamma = array_map(fn($bit) => $bit xor true, $epsilon);

	return bitsToDec($epsilon) * bitsToDec($gamma);
}

function part2 (string $input) {
	$bits = array_map(fn($line) => strToBits($line), explode("\n", $input));
	$oxy = [...$bits]; $pos = 0;
	while (count($oxy) > 1 && $pos < count($bits[0])) {
		$mc = mostCommon($oxy, $pos);
		$oxy = array_filter($oxy, fn($row) => $row[$pos] === $mc);
		$pos++;
	}
	$co2 = [...$bits]; $pos = 0;
	while (count($co2) > 1 && $pos < count($bits[0])) {
		$mc = mostCommon($co2, $pos);
		$co2 = array_filter($co2, fn($row) => $row[$pos] !== $mc);
		$pos++;
	}

	return bitsToDec(current($oxy)) * bitsToDec(current($co2));
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
