<?php

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
190: 10 19
3267: 81 40 27
83: 17 5
156: 15 6
7290: 6 8 6 15
161011: 16 10 13
192: 17 8 14
21037: 9 7 18 13
292: 11 6 16 20
INPUT;

function generate_ops (int $how_many) {
	$ops = ['*', '+'];
	for ($i=1; $i < $how_many-1; $i++) {
		$ops = A::merge(
			A::map($ops, fn($o) => $o.'*'),
			A::map($ops, fn($o) => $o.'+'),
		);
	}
	return $ops;
}

function generate_ops_join (int $how_many) {
	$ops = ['*', '+', '|'];
	for ($i=1; $i < $how_many-1; $i++) {
		$ops = A::merge(
			A::map($ops, fn($o) => $o.'*'),
			A::map($ops, fn($o) => $o.'+'),
			A::map($ops, fn($o) => $o.'|'),
		);
	}
	return $ops;
}

function calculate(array $ops, int $result, ...$nums) {
	foreach ($ops as $operations) {
		$running = $nums[0];
		for ($i=1; $i < count($nums); $i++) {
			$running = match($operations[$i-1]) {
				'*' => $running * $nums[$i],
				'+' => $running + $nums[$i],
				'|' => $running * pow(10, strlen((string)$nums[$i])) + $nums[$i]
			};
			if ($running > $result) {
				continue 2;
			}
		}
		if ($running === $result) {
			return $result;
		}
	}

	return 0;
}

function part1 (string $input) {
	$calcs = A::map(
		Str::split($input, "\n"),
		fn($l) => A::map(preg_split('/:? /', $l), fn($nr) => intval($nr))
	);

	$result = A::reduce($calcs, fn ($agr, $it) => $agr + calculate(generate_ops(count($it)-1), ...$it), 0);

	return $result;
}

function part2 (string $input) {
	$calcs = A::map(
		Str::split($input, "\n"),
		fn($l) => A::map(preg_split('/:? /', $l), fn($nr) => intval($nr))
	);

	$result = A::reduce($calcs, fn ($agr, $it) => $agr + calculate(generate_ops_join(count($it)-1), ...$it), 0);

	return $result;
}

function calc_recursive(bool $include_join_op, int $result, array $nums) : bool {
	if (1 === count($nums)) {
		return $result === $nums[0];
	}
	$nr = array_pop($nums);
	if ($nr > $result) {
		return false;
	}
	if ($include_join_op) {
		$join_reversed = str_ends_with((string) $result, (string) $nr)
			? intval(substr((string) $result, 0, -strlen((string) $nr)))
			: null;
	}

	return calc_recursive($include_join_op, $result - $nr, $nums)
	|| (($result % $nr === 0) && calc_recursive($include_join_op, (int)floor($result/$nr), $nums))
	|| ($include_join_op && !is_null($join_reversed) && calc_recursive($include_join_op, $join_reversed, $nums));
}

function part1_back (string $input) {
	$calcs = A::map(
		Str::split($input, "\n"),
		fn($l) => A::map(preg_split('/:? /', $l), fn($nr) => intval($nr))
	);

	$result = A::reduce(
		$calcs,
		function($agr, $it) {
			$res = array_shift($it);
			return calc_recursive(false, $res, $it)
				? $agr+$res
				: $agr;
		},
		0
	);

	return $result;
}

function part2_back (string $input) {
	$calcs = A::map(
		Str::split($input, "\n"),
		fn($l) => A::map(preg_split('/:? /', $l), fn($nr) => intval($nr))
	);

	$result = A::reduce(
		$calcs,
		function($agr, $it) {
			$res = array_shift($it);
			return calc_recursive(true, $res, $it)
				? $agr+$res
				: $agr;
		},
		0
	);

	return $result;
}

// PART 1
println('1+) Result of demo: ' . part1_back($input_demo));
println('1+) Result of real input: ' . part1_back($input));
println('–––');
// PART 2
println('2+) Result of demo: ' . part2_back($input_demo));
println('2+) Result of real input: ' . part2_back($input));
