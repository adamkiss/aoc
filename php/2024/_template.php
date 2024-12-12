<?php

require_once __DIR__ . '/vendor/autoload.php';

function part1 (string $input) {
	return true;
}

function part2 (string $input) {
	return true;
}

$input = @require_once(__DIR__ . '/inputs/' . basename(__FILE__));
$demoinput = <<<INPUT
PASTEDEMOINPUTHERE
INPUT;

$s = microtime(true);

// 1
$p = microtime(true);
println('1) Result of demo: ' . part1($demoinput));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
println('2) Result of demo: ' . part2($demoinput));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
