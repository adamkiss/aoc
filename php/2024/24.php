<?php

use Ds\Queue;
use Kirby\Filesystem\F;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
x00: 1
x01: 1
x02: 1
y00: 0
y01: 1
y02: 0

x00 AND y00 -> z00
x01 XOR y01 -> z01
x02 OR y02 -> z02
INPUT;
$input_demo2 = <<<INPUT
x00: 1
x01: 0
x02: 1
x03: 1
x04: 0
y00: 1
y01: 1
y02: 1
y03: 1
y04: 1

ntg XOR fgs -> mjb
y02 OR x01 -> tnw
kwq OR kpj -> z05
x00 OR x03 -> fst
tgd XOR rvg -> z01
vdt OR tnw -> bfw
bfw AND frj -> z10
ffh OR nrd -> bqk
y00 AND y03 -> djm
y03 OR y00 -> psh
bqk OR frj -> z08
tnw OR fst -> frj
gnj AND tgd -> z11
bfw XOR mjb -> z00
x03 OR x00 -> vdt
gnj AND wpb -> z02
x04 AND y00 -> kjc
djm OR pbm -> qhw
nrd AND vdt -> hwm
kjc AND fst -> rvg
y04 OR y02 -> fgs
y01 AND x02 -> pbm
ntg OR kjc -> kwq
psh XOR fgs -> tgd
qhw XOR tgd -> z09
pbm OR djm -> kpj
x03 XOR y03 -> ffh
x00 XOR y04 -> ntg
bfw OR bqk -> z06
nrd XOR fgs -> wpb
frj XOR qhw -> z04
bqk OR frj -> z07
y03 OR x01 -> nrd
hwm AND bqk -> z03
tgd XOR rvg -> z12
tnw OR pbm -> gnj
INPUT;
$input_demo3 = <<<INPUT
x00: 0
x01: 1
x02: 0
x03: 1
x04: 0
x05: 1
y00: 0
y01: 0
y02: 1
y03: 1
y04: 0
y05: 1

x00 AND y00 -> z05
x01 AND y01 -> z02
x02 AND y02 -> z01
x03 AND y03 -> z03
x04 AND y04 -> z04
x05 AND y05 -> z00
INPUT;

function process_input(string $i) : array {
	$cables = [];
	$gates = [
		0 => new SplQueue([]),
		1 => new SplQueue([]),
		2 => new SplQueue([]),
	];

	[$c_raw, $g_raw] = explode("\n\n", $i);
	foreach (explode("\n", $c_raw) as $c) {
		[$id, $v] = explode(': ', $c);
		$cables[$id] = boolval($v);
	}
	foreach (explode("\n", $g_raw) as $g) {
		[$c1, $op, $c2, , $co] = explode(' ', $g);
		if (isset($cables[$c1]) && isset($cables[$c2])) {
			$gates[2]->push([$c1, $op, $c2, $co]);
		} else if (isset($cables[$c1]) || isset($cables[$c2])) {
			$gates[1]->push([$c1, $op, $c2, $co]);
		} else {
			$gates[0]->push([$c1, $op, $c2, $co]);
		}
	}
	return [$cables, $gates];
}

function part1 (string $input) {
	/** @var array<SplQueue> $gates */
	[$cables, $gates] = process_input($input);

	foreach ([2,1,0] as $gqid) {
		$q = $gates[$gqid];
		while (!$q->isEmpty()) {
			[$c1, $op, $c2, $co] = $q->dequeue();

			if ($gqid !== 2 && (!isset($cables[$c1]) || !isset($cables[$c2]))) {
				$q->enqueue([$c1, $op, $c2, $co]);
				continue;
			}

			$cables[$co] = match($op) {
				'AND' => $cables[$c1] & $cables[$c2],
				'XOR' => $cables[$c1] ^ $cables[$c2],
				'OR' => $cables[$c1] | $cables[$c2],
			};
		}
	}

	$zs = array_reduce(array_keys($cables), function($carry, $item) use ($cables) {
		if (str_starts_with($item, 'z')) {
			$carry[$item] = $cables[$item] ? '1' : '0';
		}
		return $carry;
	}, []);
	krsort($zs);

	return intval(join('', $zs), 2);
}

function part2 (string $input) {

	return true;
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo 1: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 4);

$p = microtime(true);
$r = part1($input_demo2);
println('1) Result of demo 2: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 2024);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo3);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === true);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
