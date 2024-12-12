<?php

use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

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
	'bcfg' => 4,
	'abdfg' => 5,
	'abdefg' => 6,
	'acf' => 7,
	'abcdefg' => 8,
	'abcdfg' => 9,
];
const NUMBERS_LENGTH = [
	0 => [],
	1 => [],
	2 => [1],
	3 => [7],
	4 => [4],
	5 => [2, 3, 5],
	6 => [0, 6, 9],
	7 => [8],
	8 => [],
];

function p2_map_digit(array $map, array $digit): int {
	$rot = array_map(
		fn ($d) => $map[$d],
		$digit
	);
	sort($rot);
	$r = join('', $rot);
	if (strlen($r) > 5) {
		ray($digit, $map, $rot, NUMBERS[$r] ?? -1);
	}
	return NUMBERS[$r] ?? -1;
}

function p2_permutations() {
	$perms = [
		0 => ['']
	];
	for ($i=1; $i <= 7; $i++) {
		$perms[$i] = [];
		foreach ($perms[$i-1] as $prev) {
			foreach (PARTS as $letter) {
				$perms[$i] []= "$prev$letter";
			}
		}
	}
	return $perms[7];
}

function p2_calc_line(array $digits, array $output): int
{
	usort($digits, fn($a, $b) => Str::length($a) <=> Str::length($b));
	$digits = array_map(str_split(...), $digits);
	$f = null;

	$t = 0;

	foreach (p2_permutations() as $perm) {
		$p = str_split($perm);
		if (count(array_unique($p)) !== count($p)) {
			continue;
		}
		if ($perm === 'deafgbc') {
			rd('?');
		}
		println();
		print($perm. '=>');
		$p = array_combine($p, ['a', 'b', 'c', 'd', 'e', 'f', 'g']);
		// ksort($p);
		// cagedb
		// dcebaf

		foreach ($digits as $digit) {
			$nr = p2_map_digit($p, $digit);
			print(' '.$nr);
			if ($nr < 0) {
				continue 2;
			}
		}
		rd($p, $t);
		$f = $p;
		break;
	}

	return 0;
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
assert($r === 5335);

$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 61229);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
