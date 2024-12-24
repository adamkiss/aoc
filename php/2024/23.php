<?php

use Ds\PriorityQueue;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
kh-tc
qp-kh
de-cg
ka-co
yn-aq
qp-ub
cg-tb
vc-aq
tb-ka
wh-tc
yn-cg
kh-ub
ta-co
de-co
tc-td
tb-wq
wh-td
ta-ka
td-qp
aq-cg
wq-ub
ub-vc
de-ta
wq-aq
wq-vc
wh-yn
ka-de
kh-ta
co-tc
wh-qp
tb-vc
td-yn
INPUT;

function process_input(string $input): array {
	$conn = [];
	foreach (explode("\n", $input) as $c) {
		[$pc1, $pc2] = explode('-', $c);
		$conn[$pc1][$pc2] = true;
		$conn[$pc2][$pc1] = true;
	}
	return $conn;
}

function part1 (string $input) {
	$net = process_input($input);
	$tt = [];
	foreach ($net as $pc1 => $conn) {
		foreach ($conn as $pc2 => $_) {
			foreach ($net[$pc2] as $pc3 => $_) {
				if ($pc3 === $pc1) {
					continue;
				}
				if (isset($net[$pc3][$pc1])) {
					if (!($pc1[0] === 't' || $pc2[0] === 't' || $pc3[0] === 't')) {
						continue;
					}
					$arr = [$pc1, $pc2, $pc3];
					sort($arr);
					$tt[join(',', $arr)] = true;
				}
			}
		}
	}
	return count($tt);
}

function part2 (string $input) {
	$net = process_input($input);

	$q = new PriorityQueue();
	$checked = [];
	$maxnet = [];
	$maxnetc = 0;

	foreach ($net as $pc1 => $conn) {
		foreach (array_keys($conn) as $pc2) {
			$q->push([$pc2, [$pc1 => true]], 1);
		}
	}

	while(!$q->isEmpty()) {
		[$pc, $group] = $q->pop();
		$gc = count($group);

		if (isset($group[$pc])) {
			$checked[$pc] = true;
			if ($gc < $maxnetc) {
				println("Remaining in queue: {$q->count()}, Length $gc");
				fwrite(STDOUT, "\e[1A"); // up

				continue;
			}

			$maxnet = $group;
			$maxnetc = $gc;
			continue;
		}

		if (isset($checked[$pc])) {
			continue;
		}

		foreach ($group as $tpc => $_) {
			if (!isset($net[$tpc][$pc])) {
				continue 2;
			}
		}

		foreach ($net[$pc] as $npc => $_) {
			$k = join(',', [$npc, $pc, ...array_keys($group)]);
			$q->push([$npc, [$pc => true, ...$group]], $gc + 1);
		}
	}

	$maxnet = array_keys($maxnet);
	sort($maxnet);
	ray($maxnet);

	return join(',', $maxnet);
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 7);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 'co,de,ka,ta');

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
