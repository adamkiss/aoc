<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
029A
980A
179A
456A
379A
INPUT;

const KEYPAD = [
	'7' => [0, 0],
	'8' => [1, 0],
	'9' => [2, 0],
	'4' => [0, 1],
	'5' => [1, 1],
	'6' => [2, 1],
	'1' => [0, 2],
	'2' => [1, 2],
	'3' => [2, 2],
	'0' => [1, 3],
	'A' => [2, 3],
];
const ARROWPAD = [
	'A' => [
		'A' => 'A',
		'>' => 'vA',
		'^' => '<A',
		'v' => '<vA',
		'<' => 'v<<A',
	],
	'>' => [
		'A' => '^A',
		'>' => 'A',
		'^' => '<^A',
		'v' => '<A',
		'<' => '<<A',
	],
	'^' => [
		'A' => '>A',
		'>' => 'v>A',
		'^' => 'A',
		'v' => 'vA',
		'<' => 'v<A',
	],
	'v' => [
		'A' => '^>A',
		'>' => '>A',
		'^' => '^A',
		'v' => 'A',
		'<' => '<A',
	],
	'<' => [
		'A' => '>>^A',
		'>' => '>>A',
		'^' => '>^A',
		'v' => '>A',
		'<' => 'A',
	],
];
const DEBUG = false;

$cache = [];

function solveArrowpadsStr(string $for, int $level) : string {
	// PRINT_DEBUG4LYFE
	if (DEBUG) {
		println($level, $for);
	}
	global $cache;
	if ($level === 0) {
		return $for;
	}
	$sum = '';
	foreach (explode('-', str_replace('A', 'A-', $for)) as $cut) {
		if ($cut === '') {
			continue;
		}
		if ($cut === 'A') {
			$sum .= 'A';
			continue;
		}
		$id = $cut.$level;
		if (!isset($cache[$id])) {
			$cA = "A{$cut}";
			$str = '';
			for ($i=0; $i < strlen($cA)-1; $i++) {
				$str .= ARROWPAD[$cA[$i]][$cA[$i + 1]];
			}
			$cache[$id] = solveArrowpadsStr($str, $level - 1);
		}
		$sum .= $cache[$id];
	}
	if (DEBUG) {
		println('S'.$level, $sum);
	}
	return $sum;
}

function solveArrowpads(string $for, int $level) : int {
	global $cache;
	if ($level === 0) {
		return strlen($for);
	}
	$sum = 0;
	foreach (explode('-', str_replace('A', 'A-', $for)) as $cut) {
		if ($cut === '') {
			continue;
		}
		if ($cut === 'A') {
			$sum += 1;
			continue;
		}
		$id = $cut.$level;
		if (!isset($cache[$id])) {
			$cA = "A{$cut}";
			$str = '';
			for ($i=0; $i < strlen($cA)-1; $i++) {
				$str .= ARROWPAD[$cA[$i]][$cA[$i + 1]];
			}
			$cache[$id] = solveArrowpads($str, $level - 1);
		}
		$sum += $cache[$id];
	}
	return $sum;
}

function solveStr(string $c, int $levels): string {
	if (DEBUG) {
		println($levels, $c);
	}
	$cA = "A{$c}";
	$str = '';
	for ($j=0; $j < strlen($cA)-1; $j++) {
		[$ax, $ay] = KEYPAD[$cA[$j+1]];
		[$bx, $by] = KEYPAD[$cA[$j]];
		$dx = $ax - $bx;
		$dy = $ay - $by;


		if ($dx < 0 && $ax === 0 && $by === 3) {
			if ($dx === -2) {
				$str .= '<';
			}
			$str .= str_repeat($dy < 0 ? '^' : 'v', abs($dy));
			$str .= '<';
		} else {
			$str .= str_repeat($dx < 0 ? '<' : '>', abs($dx));
			$str .= str_repeat($dy < 0 ? '^' : 'v', abs($dy));
		}
		$str .= 'A';
	}
	return solveArrowpadsStr($str, $levels - 1);
}

function solve(string $c, int $levels): int {
	$cA = "A{$c}";
	$str = '';
	for ($j=0; $j < strlen($cA)-1; $j++) {
		[$ax, $ay] = KEYPAD[$cA[$j+1]];
		[$bx, $by] = KEYPAD[$cA[$j]];
		$dx = $ax - $bx;
		$dy = $ay - $by;

		if ($dx < 0 && $ax === 0 && $by === 3) {
			$str .= str_repeat($dy < 0 ? '^' : 'v', abs($dy));
			$str .= str_repeat('<', -$dx);
		} else {
			if ($dx < 0) {
				$str .= str_repeat('<', -$dx);
				$str .= str_repeat($dy < 0 ? '^' : 'v', abs($dy));
			} else {
				$str .= str_repeat($dy < 0 ? '^' : 'v', abs($dy));
				$str .= str_repeat($dx < 0 ? '<' : '>', abs($dx));
			}
		}
		$str .= 'A';
	}

	return solveArrowpads($str, $levels - 1);
}

function part1 (string $input) {
	$codes = array_reduce(explode("\n", $input), function ($acc, $c) {
		$acc[$c] = intval($c, 10);
		return $acc;
	}, []);

	$sum = 0;
	foreach ($codes as $c => $cint) {
		// println($c);
		// println(solveStr($c, 3));
		$sum += $cint * solve($c, 3);
	}

	return $sum;
}

function part2 (string $input) {
	$codes = array_reduce(explode("\n", $input), function ($acc, $c) {
		$acc[$c] = intval($c, 10);
		return $acc;
	}, []);

	$sum = 0;
	foreach ($codes as $c => $cint) {
		$sum += $cint * solve($c, 26);
	}

	return $sum;
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 126384);

$p = microtime(true);
$r = part1($input);
println('1) Result of real input: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 128962);

// 2
$p = microtime(true);
$r = part2($input);
println('2) Result of real input: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r !== 189612006449478); // incorrect
assert($r !== 156714718114678); // incorrect
assert($r > 156714718114678 && $r < 189612006449478);
// assert($r !== 159684145150108); // found smaller (LOL)

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
