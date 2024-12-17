<?php

use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input('17.txt');

function process_input(string $i) {
	[$_, $ra, $rb, $rc, $p] = Str::match($i, '/.*A: (\d*)\n.*B: (\d*)\n.*C: (\d*)\n\n.*: (.*)/');
	$pr = Str::replace($p, ',', '');
	$ra = intval($ra);
	$rb = intval($rb);
	$rc = intval($rc);
	return [$ra, $rb, $rc, $pr, $p];
}

function part1 (string|array $i, bool $o_eq_p_br = false, bool $debug = false) {
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
		if ($debug) {
			println('|···1··2··3··4··5··6··7··8··9··|··|··|··');
			println('A', base_convert($ra, 10, 2));
			println('B', base_convert($rb, 10, 2));
			println('C', base_convert($rc, 10, 2));
			printf(
				"%s: %s (%s = %s) A: %s  B: %s  C: %s  O: [%s]\n",
				$ptr, $instr, $op,
				match($instr) { '3', '4' => '_', '1' => $literal($op), default => $combo($op) },
				$ra, $rb, $rc, join(',', $out)
			);
		}
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
		if ($o_eq_p_br && $instr === '5' && $out[count($out)-1] != $p[count($out)-1]) {
			return false;
		}
		$ptr = ($instr === '3' && $ra !== 0)
			? $literal($op)
			: $ptr + 2;
		if ($debug) {
			printf(
				" -> %s -> A: %s  B: %s  C: %s  O: [%s]%s\n",
				$ptr, $ra, $rb, $rc, join(',', $out), $instr === '5' ? " ========\n\n\n" : ''
			);
		}
	}
	return $out;
}

function part2 (string $i, array $argv) {
	[$ra, $rb, $rc, $p, $pc] = process_input($i);
	if (count($argv) === 2) {
		if ($argv[1] === 'd') {
			$debug = true;
		} else {
			$ra = base_convert($argv[1], 8, 10);
		}
	}

	foreach ([2,4,1,1,7,5,1,4,0,3,4,5,5,5,3,0] as $v) {
		print(sprintf('%03s', decbin($v)));
	}
	println();

	$res = part1([$ra, $rb, $rc, $p]);

	foreach ($res as $rv) {
		print(sprintf('%03s ', decbin($rv)));
	}
	println();

	return $res;
}

// compile regex beforehand 😄
process_input($input);

$s = microtime(true);
println('O: ' . join(',', part2($input, $argv)));
printf("» %.3fms\n", (microtime(true)-$s) * 1000);
//
// A = 0
// B = 0
//
// B ^ 4
// B ^ 1

/**
INPUT IN 2, split:
011 111 011 000 001 101 011 000 001

⮑rev:
110 111 110 000 100 101 110 000 100

⮑IN DEC
6 7 6 0 4 5 6 0 4

OUT
5 1 4 0 5 1 0 2 6

NOPE


STEPS IN 2: (obv clue)
011 111 011 000 001 101 011 000 001 -> 001 [5] 101
011 111 011 000 001 101 011 000  -> 000 [1] 001
011 111 011 000 001 101 011  -> 011 [4] 100
011 111 011 000 001 101 -> 101 [0] 000
011 111 011 000 001 -> 001 [5] 101
011 111 011 000  -> 000 [1] 001
011 111 011 -> 011 [0] 000
011 111 -> 111 [2] 010
011 -> 011 [6] 110
0

110 010 000 001 101 000 100 001 101 res
101 101 011 001 100 101 111 110 100 n ^ ? = res

011 111 011 000 001 101 011 000 001 -> 001 [6] 110
011 111 011 000 001 101 011 000 -> 000 [2] 010
011 111 011 000 001 101 011 -> 011 [0] 000
011 111 011 000 001 101 -> 101 [1] 001
011 111 011 000 001 -> 001 [5] 101
011 111 011 000 -> 000 [0] 000
011 111 011 -> 011 [4] 100
011 111 -> 111 [1] 001
011 -> 011 [5] 101
0

101 001 100 000 101 001 000 010 110 res
110 110 000 111 100 100 011 010 111 n ^ ? = res

011 111 011 000 001 101 011 000 001

110 111 110 000 100 101 110 000 100
6   7   6   0   4   5   6   0   4

100 000 110 101 100 000 110 111 110 XOR
4   0   6   5   4   0   5   7   6



*/
