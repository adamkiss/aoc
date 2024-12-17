<?php

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
Register A: 729
Register B: 0
Register C: 0

Program: 0,1,5,4,3,0
INPUT;
$input_demo1 = <<<INPUT
Register A: 10
Register B: 0
Register C: 0

Program: 5,0,5,1,5,4
INPUT;
$input_demo2 = <<<INPUT
Register A: 2024
Register B: 0
Register C: 0

Program: 0,1,5,4,3,0
INPUT;
$input_demo3 = <<<INPUT
Register A: 2024
Register B: 0
Register C: 0

Program: 0,3,5,4,3,0
INPUT;


function process_input(string $i) {
	[$_, $ra, $rb, $rc, $p] = Str::match($i, '/.*A: (\d*)\n.*B: (\d*)\n.*C: (\d*)\n\n.*: (.*)/');
	$pr = Str::replace($p, ',', '');
	$ra = intval($ra);
	$rb = intval($rb);
	$rc = intval($rc);
	return [$ra, $rb, $rc, $pr, $p];
}

function part1 (string|array $i, bool $joined = true) {
	[$ra, $rb, $rc, $p] = is_array($i) ? $i : process_input($i);

	$ri = $ra;

	$out = [];
	$ptr = 0;
	$end = strlen($p);

	while ($ptr < $end-1) {
		$instr = $p[$ptr];
		$op = $p[$ptr + 1];

		$literal = fn ($ch) => intval($ch);
		$combo = fn($ch) => match($ch) {
			'0','1','2','3' => $literal($ch),
			'4' => $ra,
			'5' => $rb,
			'6' => $rc,
			default => throw new Error('NOPE')
		};
		match (true) {
			'0' === $instr => $ra = (int)floor($ra / pow(2, $combo($op))),
			'1' === $instr => $rb ^= $literal($op),
			'2' === $instr => $rb = $combo($op) % 8,
			'3' === $instr => true,
			'4' === $instr => $rb ^= $rc,
			'5' === $instr => $out []= $combo($op) % 8,
			'6' === $instr => $rb = (int)floor($ra / pow(2, $combo($op))),
			'7' === $instr => $rc = (int)floor($ra / pow(2, $combo($op))),
		};
		$ptr = ($instr === '3' && $ra !== 0)
			? $literal($op)
			: $ptr + 2;
	}

	return $joined ? join(',', $out) : $out;
}

function part2_brute (string $i) {
	[$r_, $rb, $rc, $p, $pc] = process_input($i);
	for ($i=0; $i < 10000000; $i++) {
		$res = part1([$i, $rb, $rc, $p]);
		if ($res === $pc) {
			return $i;
		}
	}
	return 'NOPE';
}

function arr_to_bin(array $a): string {
	return join(' ', array_map(fn ($i) => sprintf('%03s', decbin($i)), $a));
}

function part2_recurse(string $t, string $num, int $rb, int $rc, string $p): string|false {
	$r = part1([base_convert($num, 8, 10), $rb, $rc, $p], joined: false);

	if (count($r) !== strlen($num)) {
		return false;
	}
	$rbin = arr_to_bin($r);
	if ($t === $rbin) {
		return $num;
	}
	if (!str_ends_with($t, $rbin)) {
		return false;
	}

	for ($i=0; $i < 8; $i++) {
		$n = part2_recurse($t, $num.$i, $rb, $rc, $p);
		if (is_string($n)) {
			return $n;
		}
	}

	return false;
}

function part2_recursive(string $i) {
	[$ra, $rb, $rc, $p, $pc] = process_input($i);

	$pc_arr = explode(',', $pc);
	$target = arr_to_bin($pc_arr);

	$test = A::fill([], 8, fn ($i) => (string)$i);
	foreach ($test as $s) {
		if ($nr = part2_recurse($target, $s, $rb, $rc, $p)) {
			return base_convert($nr, 8, 10);
		}
	}

	return 'FAIL';
}

function part2 (string $i) {
	[$ra, $rb, $rc, $p, $pc] = process_input($i);

	$pc_arr = explode(',', $pc);
	$target = arr_to_bin($pc_arr);

	$test = [''];

	for ($pos=1; $pos <= count($pc_arr); $pos++) {
		$test = A::reduce(
			$test,
			function ($acc, $i) {
				for ($j=0; $j < 8; $j++) {
					$acc [] = $i . $j;
				}
				return $acc;
			},
			[]
		);
		$test = array_filter(
			$test,
			function($oct) use ($rb, $rc, $p, $target) {
				$dec = base_convert($oct, 8, 10);
				$res = part1([$dec, $rb, $rc, $p], joined: false);
				if (count($res) !== strlen($oct)) {
					return false;
				}
				return str_ends_with($target, arr_to_bin($res));
			}
		);
	}
	return base_convert(A::first($test), 8, 10);
}

// compile regex beforehand 😄
process_input($input_demo);

$s = microtime(true);

$p = microtime(true);
println(part1([0,0,9,'26']));
println(part1([0,29,0,'17']));
println(part1([0,2024,43690,'40']));
printf("INTR » %.3fms\n\n", (microtime(true)-$p) * 1000);

// 1
$p = microtime(true);
$r = part1($input_demo1);
println('1) Result of demo1: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === '0,1,2');

$p = microtime(true);
$r = part1($input_demo2);
println('1) Result of demo2: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === '4,2,5,6,7,7,7,7,3,1,0');

$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === '4,6,3,5,6,3,5,2,1,0');

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo3);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r == 117440);

// $p = microtime(true);
// println('2) Result of real input: ' . part2($input));
// printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('2) Result of real input: ' . part2_recursive($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
