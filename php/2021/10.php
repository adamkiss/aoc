<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
[({(<(())[]>[[{[]{<()<>>
[(()[<>])]({[<{<<[]>>(
{([(<{}[<>[]}>{[]{[(<()>
(((({<>}<{<{<>}{[]{[]{}
[[<[([]))<([[{}[[()]]]
[{[{({}]{}}([{[{{{}}([]
{<[[]]>}<{[{[{[]{()[[[]
[<(<(<(<{}))><([]([]()
<{([([[(<>()){}]>(<<{{
<{([{{}}[<[[[<>{}]]]>[]]
INPUT;

const VALUE = [
	')' => 3,
	']' => 57,
	'}' => 1197,
	'>' => 25137,
];

const VALUE_P2 = [
	')' => 1,
	']' => 2,
	'}' => 3,
	'>' => 4,
];

const CLOSECHR = [
	'(' => ')',
	'[' => ']',
	'{' => '}',
	'<' => '>',
];

function opens (string $ch) {
	return match($ch) {
		'(', '[', '{', '<' => true,
		')', ']', '}', '>' => false,
	};
}

function part1 (string $input) {
	$c = [];
	foreach (explode("\n", $input) as $ns) {
		$q = new SplStack();
		for ($i=0; $i < strlen($ns); $i++) {
			$ch = $ns[$i];
			if(opens($ns[$i])) {
				$q->push($ns[$i]);
				continue;
			}

			$should_close = $q->pop();
			if ($ch !== CLOSECHR[$should_close]) {
				$c [] = $ch;
				break;
			}
		}
	}
	return array_sum(array_map(fn($ch) => VALUE[$ch], $c));
}

function part2 (string $input) {
	$scores = [];
	foreach (explode("\n", $input) as $ns) {
		$q = new SplStack();
		// println($ns);
		for ($i=0; $i < strlen($ns); $i++) {
			$ch = $ns[$i];
			if(opens($ns[$i])) {
				$q->push($ns[$i]);
				continue;
			}

			$should_close = $q->top();
			if ($ch !== CLOSECHR[$should_close]) {
				// println("DISCARDED ON {$should_close} {$ch}");
				continue 2;
			}

			$q->pop();
		}

		$sc = 0;
		while(! $q->isEmpty()) {
			$sc *= 5;
			$sc += VALUE_P2[CLOSECHR[$q->pop()]];
		}
		$scores [] = $sc;
	}
	sort($scores);
	return $scores[ceil(count($scores) / 2) - 1];
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 26397);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 288957);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
