<?php

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';
// DEAFGBC
$input = read_input();
$input_demo = <<<INPUT
be cfbegad cbdgef fgaecd cgeb fdcge agebfd fecdb fabcd edb | fdgacbe cefdb cefbgd gcbe
edbfga begcd cbg gc gcadebf fbgde acbgfd abcde gfcbed gfec | fcgedb cgb dgebacf gc
fgaebd cg bdaec gdafb agbcfd gdcbef bgcad gfac gcb cdgabef | cg cg fdcagb cbg
fbegcd cbd adcefb dageb afcb bc aefdc ecdab fgdeca fcdbega | efabcd cedba gadfec cb
aecbfdg fbg gf bafeg dbefa fcge gcbea fcaegb dgceab fcbdga | gecf egdcabf bgf bfgea
fgeab ca afcebg bdacfeg cfaedg gcfdb baec bfadeg bafgc acf | gebdcfa ecba ca fadegcb
dbcfg fgd bdegcaf fgec aegbdf ecdfab fbedc dacgb gdcebf gf | cefg dcbef fcge gbcadfe
bdfegc cbegaf gecbf dfcage bdacg ed bedf ced adcbefg gebcd | ed bcgafe cdgba cbgef
egadfb cdbfeg cegd fecab cgb gbdefca cg fgcdab egfdb bfceg | gbdfcae bgc cg cgb
gcafb gcf dcaebfg ecagb gf abcdeg gaef cafbge fdbac fegbdc | fgae cfgab fg bagce
INPUT;
$input_simple = <<<INPUT
acedgfb cdfbe gcdfa fbcad dab cefabd cdfgeb eafb cagedb ab | cdfeb fcadb cdfeb cdbaf
INPUT;

function process_input(string $input) : array {
	return array_map(
		array: explode("\n", $input),
		callback: function($line) {
			[$digits, $output] = explode(' | ', $line);
			return [
				'digits' => explode(' ', $digits),
				'output' => explode(' ', $output),
			];
		}
	);
}

function part1 (string $input) {
	$input = process_input($input);
	$allOutput = array_merge(...array_map(fn($l) => $l['output'], $input));

	$easyDigits = count(
		array_filter(
			$allOutput,
			fn(string $digit) => in_array(Str::length($digit), [2, 3, 4, 7])
		)
	);

	return $easyDigits;
}

const PARTS = ['a','b', 'c', 'd', 'e', 'f', 'g'];
const NUMBERS = [
	'abcefg' => 0,
	'cf' => 1,
	'acdeg' => 2,
	'acdfg' => 3,
	'bcdf' => 4,
	'abdfg' => 5,
	'abdefg' => 6,
	'acf' => 7,
	'abcdefg' => 8,
	'abcdfg' => 9,
];
const SEGMENTS = [
	0 => ['a', 'b', 'c', 'e', 'f', 'g'],
	1 => ['c', 'f'],
	2 => ['a', 'c', 'd', 'e', 'g'],
	3 => ['a', 'c', 'd', 'f', 'g'],
	4 => ['b', 'c', 'd', 'f'],
	5 => ['a', 'b', 'd', 'f', 'g'],
	6 => ['a', 'b', 'd', 'e', 'f', 'g'],
	7 => ['a', 'c', 'f'],
	8 => ['a', 'b', 'c', 'd', 'e', 'f', 'g'],
	9 => ['a', 'b', 'c', 'd', 'f', 'g'],
];

function array_exclude(array $array, array $exclude): array {
	return array_values(array_filter($array, fn($el) => !in_array($el, $exclude)));
}
function array_filter_v(array $array, callable $callback): array {
	return array_values(array_filter($array, $callback));
}
function av(array $array): array {
	return array_values($array);
}
function array_contains(array $array, array $contains): bool {
	return count(array_intersect($array, $contains)) === count($contains);
}

function p2_calc_line(array $digits, array $output): int
{
	/*
	  A
	B   C
	  D
	E   F
	  G
	*/
	usort($digits, fn($a, $b) => Str::length($a) <=> Str::length($b));
	$digits = array_map(str_split(...), $digits);

	$m = [
		'a' => null,
		'b' => null,
		'c' => null,
		'd' => null,
		'e' => null,
		'f' => null,
		'g' => null,
	];
	$one = A::find($digits, fn($d) => count($d) === 2);
	$four = A::find($digits, fn($d) => count($d) === 4);
	$seven = A::find($digits, fn($d) => count($d) === 3);
	$eight = A::find($digits, fn($d) => count($d) === 7);

	// A = 7-1
	$m['a'] = array_exclude($seven, $one)[0];

	$bd = array_diff($four, $one);
	$five = array_filter_v(
		$digits,
		fn($d) => count($d) === 5 && array_contains($d, $bd)
	)[0];
	$m['f'] = av(array_intersect($five, $one))[0];

	$almost_nine = array_merge($four, [$m['a']]);
	$nine = av(array_filter($digits, fn($d) => count($d) === 6 && count(array_diff($d, $almost_nine)) === 1))[0];
	$m['g'] = av(array_diff($nine, $almost_nine))[0];

	$m['c'] = av(array_diff($one, [$m['f']]))[0];
	$three = av(array_filter($digits, fn($d) => count($d) === 5 && count(array_intersect($d, $m)) === 4))[0];
	$m['d'] = av(array_diff($three, $m))[0];
	$m['b'] = av(array_diff($bd, [$m['d']]))[0];
	$m['e'] = av(array_diff($eight, $m))[0];

	// !!!!!
	$m = array_flip($m);

	$r = '';
	foreach ($output as $i => $digit) {
		$d = [];
		foreach (str_split($digit) as $i => $char) {
			$d []= $m[$char];
		}
		sort($d);
		$r .= NUMBERS[implode('', $d)] ?? 'X';
	}
	return intval($r);
}

function part2 (string $input) {
	$input = process_input($input);
	$numbers = array_map(
		fn($line) => p2_calc_line($line['digits'], $line['output']),
		$input
	);

	return array_sum($numbers);
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
$r = part2($input_simple);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 5353);

$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 61229);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
