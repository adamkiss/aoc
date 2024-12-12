<?php

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
47|53
97|13
97|61
97|47
75|29
61|13
75|53
29|13
97|29
53|29
61|53
97|53
61|29
47|13
75|47
97|75
47|61
75|61
47|29
75|13
53|13

75,47,61,53,29
97,61,53,29,13
75,29,13
75,97,47,61,53
61,13,29
97,13,75,29,47
INPUT;

function process_input (string $input): array {
	$rules = [];
	$prints = [];
	[$rules_raw, $prints_raw] = explode("\n\n", $input);
	foreach (Str::split($rules_raw, "\n") as $rr) {
		$rules [] = Str::split($rr, '|');
	}
	foreach (Str::split($prints_raw, "\n") as $pr) {
		$prints [] = Str::split($pr, ',');
	}
	return [$rules, $prints];
}

function part1 (string $input) {
	[$rules, $prints] = process_input($input);
	$correct = [];
	foreach ($prints as $print) {
		foreach ($rules as $rule) {
			$pos0 = false;
			$pos1 = false;
			foreach ($print as $idx => $value) {
				if ($rule[0] === $value) {
					$pos0 = $idx;
				} else if ($rule[1] === $value) {
					$pos1 = $idx;
				}
			}
			if ($pos0 !== false && $pos1 !== false && $pos0 > $pos1) {
				continue 2;
			}
		}
		$correct [] = $print;
	}
	$sum = A::reduce($correct, function($agr, $it) {
		$middle = (int)floor(count($it) / 2);
		return $agr + intval($it[$middle]);
	}, 0);
	return $sum;
}

function part2 (string $input) {
	[$rules, $prints] = process_input($input);
	$incorrect = [];
	foreach ($prints as $print) {
		foreach ($rules as $rule) {
			$pos0 = false;
			$pos1 = false;
			foreach ($print as $idx => $value) {
				if ($rule[0] === $value) {
					$pos0 = $idx;
				} else if ($rule[1] === $value) {
					$pos1 = $idx;
				}
			}
			if ($pos0 !== false && $pos1 !== false && $pos0 > $pos1) {
				$incorrect [] = $print;
				continue 2;
			}
		}
	}
	$sum = 0;
	foreach ($incorrect as $ic) {
		usort($ic, function($l, $r) use ($rules) {
			if (in_array([$l, $r], $rules)) {
				return 1;
			}
			if (in_array([$r, $l], $rules)) {
				return -1;
			}
			return 0;
		});
		$middle = (int)floor(count($ic) / 2);
		$sum += intval($ic[$middle]);
	}
	return $sum;
}

// PART 1
println('1) Result of demo: ' . part1($input_demo));
println('1) Result of real input: ' . part1($input));
println('–––');
// PART 2
println('2) Result of demo: ' . part2($input_demo));
println('2) Result of real input: ' . part2($input));
