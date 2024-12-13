<?php

use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
Button A: X+94, Y+34
Button B: X+22, Y+67
Prize: X=8400, Y=5400

Button A: X+26, Y+66
Button B: X+67, Y+21
Prize: X=12748, Y=12176

Button A: X+17, Y+86
Button B: X+84, Y+37
Prize: X=7870, Y=6450

Button A: X+69, Y+23
Button B: X+27, Y+71
Prize: X=18641, Y=10279
INPUT;
$input_demo_2 = <<<INPUT
Button A: X+94, Y+34
Button B: X+22, Y+67
Prize: X=10000000008400, Y=10000000005400

Button A: X+26, Y+66
Button B: X+67, Y+21
Prize: X=10000000012748, Y=10000000012176

Button A: X+17, Y+86
Button B: X+84, Y+37
Prize: X=10000000007870, Y=10000000006450

Button A: X+69, Y+23
Button B: X+27, Y+71
Prize: X=10000000018641, Y=10000000010279
INPUT;

const COST_A = 3;
const COST_B = 1;

function process_input(string $input) : array {
	$machines = explode("\n\n", $input);
	foreach ($machines as $i => $m) {
		$m = Str::match(
			$m,
			'/.*X\+(\d+), Y\+(\d+)\n.*X\+(\d+), Y\+(\d+)\n.*X=(\d+), Y=(\d+)/'
		);
		array_splice($m, 0, 1, []);
		$machines[$i] = array_map('intval', $m);
	}
	return $machines;
}

function process_input_no_regex(string $input) : array {
	$machines = explode("\n\n", $input);
	foreach ($machines as $i => $m) {
		$nrs = [];
		$j = 0;
		foreach (str_split($m) as $ch) {
			if ($ch === '+' || $ch==='=') {
				$nrs[$j] = [];
				continue;
			}
			if ($ch === ',' || $ch==="\n") {
				$j++;
				continue;
			}
			if (!is_numeric($ch)) {
				continue;
			}

			$nrs[$j] []= $ch;
		}
		$machines[$i] = array_map(fn($n) => intval(join('', $n)), $nrs);
	}
	return $machines;
}

function solve_machine_naive($ax, $ay, $bx, $by, $px, $py) {
	$maxa = (int)floor(max($px / $ax, $py / $ay));
	$maxb = (int)floor(max($px / $bx, $py / $by));
	$s = [];
	// max 100 presses
	for ($ia=0; $ia < min($maxa, 101); $ia++) {
		for ($ib=0; $ib < min($maxb, 101); $ib++) {
			// too much: break further loops
			if (($ax * $ia) + ($bx * $ib) > $px) { break; }
			if (($ay * $ia) + ($by * $ib) > $py) { break; }
			// not enough: continue next loop
			if (($ax * $ia) + ($bx * $ib) < $px) { continue; }
			if (($ay * $ia) + ($by * $ib) < $py) { continue; }
			// both fit:
			$s [] = [$ia * COST_A + $ib * COST_B, $ia, $ib];
		}
	}
	return $s;
}

function solve_machine_cramer($ax, $ay, $bx, $by, $cx, $cy) {
	// $cx += 10000000000000;
	// $cy += 10000000000000;
	// cramer's rule: matrix solving https://byjus.com/maths/cramers-rule/
	// | $a1 $b1 |  | x |  | $c1 |
	// | $a2 $b2 |  | y |  | $c2 |
	//
	// A B = button presses = unknown
	// A * ax + B * bx = cx
	// A * ay + B * by = cy
	//
	// | $ax $bx |  | A |  | $cx |
	// | $ay $by |  | B |  | $cy |
	//
	// | $cx $bx |
	// | $cy $by |
	//
	// | $ax $cx |
	// | $ay $cy |
	// I have no idea???

	$D  = $ax * $by - $bx * $ay;
	if ($D === 0) {
		// hasn't got unique solution
		return null;
	} else {
		$Da = $cx * $by - $bx * $cy;
		$Db = $ax * $cy - $cx * $ay;

		$A = $Da / $D;
		$B = $Db / $D;

		if (!(is_int($A) && is_int($B))) {
			return null;
		}

		return [$A * COST_A + $B * COST_B, $A, $B ];
	}
}

function part1 (string $input) {
	$m = process_input($input);
	$tc = 0;
	foreach ($m as $i => $machine) {
		$s = solve_machine_cramer(...$machine);
		if (is_null($s)) {
			continue;
		}
		$tc += $s[0];
	}
	return $tc;
}

function part2 (string $input) {
	$m = process_input($input);
	$tc = 0;
	foreach ($m as $i => $machine) {
		// add 10000000000000 to $cx, $cy
		$machine[4] += 10000000000000;
		$machine[5] += 10000000000000;
		$s = solve_machine_cramer(...$machine);
		if (is_null($s)) {
			continue;
		}
		$tc += $s[0];
	}
	return $tc;
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r, 480);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r, 1);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
