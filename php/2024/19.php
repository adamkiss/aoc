<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
r, wr, b, g, bwu, rb, gb, br

brwrr
bggr
gbbr
rrbgbr
ubwu
bwurrg
brgr
bbrgwb
INPUT;

function process_input(string $input): array {
	[$towels_raw, $designs] = explode("\n\n", $input);

	$towels = [];
	$designs = explode("\n", $designs);
	foreach (explode(', ', $towels_raw) as $t) {
		if (!isset($towels[strlen($t)])) {
			$towels[strlen($t)] = [$t => true];
		} else {
			$towels[strlen($t)][$t] = true;
		}
	}
	ksort($towels);
	return [$towels, $designs];
}

function find_shortest(array $t, string $d, int $maxtlen) : int {
	$q = new SplPriorityQueue();
	$q->insert([0, 0, []], 0);
	$tarlen = strlen($d);

	$found = 0;
	while (!$q->isEmpty()) {
		[$offset, $parts] = $q->extract();

		if ($offset === $tarlen) {
			return $parts;
		}

		$parts++;
		for ($j=$maxtlen; $j > 0; $j--) {
			if ($offset+$j > $tarlen) {
				continue;
			}
			if (!isset($t[$j])) {
				continue;
			}
			$match = substr($d, $offset, $j);

			if (!isset($t[$j][$match])) {
				continue;
			}
			$q->insert([$offset + $j, $parts], $parts);
		}

	}
	return -1;
}

function find_all(array &$cache, array $t, string $d, int $maxtlen, int $l = 0) : int {
	if ($d === '') {
		return 1;
	}
	if (isset($cache[$d])) {
		return $cache[$d];
	}
	$found = 0;
	for($j = 1; $j <= $maxtlen; $j++) {
		if ($j > strlen($d)) {
			continue;
		}
		if (!isset($t[$j])) {
			continue;
		}
		$m = substr($d, 0, $j);
		$rem = substr($d, $j);
		if (!isset($t[$j][$m])) {
			continue;
		}
		$remc = find_all($cache, $t, $rem, $maxtlen, $l + 1);
		$cache[$rem] = $remc;
		$found += $remc;
	}
	return $found;
}

function part1 (string $input) {
	[$t, $designs] = process_input($input);
	$ml = array_keys($t)[count($t) - 1];

	$possible = array_filter(
		$designs,
		fn ($d) => find_shortest($t, $d, $ml) !== -1
	);

	return count($possible);
}

function part2 (string $input) {
	[$t, $designs] = process_input($input);
	$ml = array_keys($t)[count($t) - 1];

	$designs = array_filter(
		$designs,
		fn ($d) => find_shortest($t, $d, $ml) !== -1
	);

	$cache = [];
	$sum = 0;
	foreach ($designs as $d) {
		$sum += find_all($cache, $t, $d, $ml);
	}
	return $sum;
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 6);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 16);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
