<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
2333133121414131402
INPUT;

function sumArray(array $arr) {
	$sum = 0;
	foreach ($arr as $i=>$v) {
		if (is_null($v)) {
			continue;
		}
		$sum += $i * $v;
	}
	return $sum;
}

function part1 (string $input) {
	$nr_index = 0;
	$nr_next = true;
	$numbers = array_reduce(str_split($input), function ($carry, $item) use (&$nr_index, &$nr_next) {
		for ($i=0; $i < intval($item); $i++) {
			$carry []= $nr_next ? $nr_index : null;
		}
		if ($nr_next) {
			$nr_index++;
		}
		$nr_next = !$nr_next;
		return $carry;
	}, []);

	$i_current = 0;
	$i_fill = count($numbers) - 1;

	while ($i_current < $i_fill) {
		if (!is_null($numbers[$i_current])) {
			$i_current++;
			continue;
		}

		if (is_null($numbers[$i_fill])) {
			$i_fill--;
			continue;
		}

		$numbers[$i_current] = $numbers[$i_fill];
		$numbers[$i_fill] = null;

		$i_current++;
		$i_fill--;
	}
	return sumArray($numbers);
}

function part2 (string $input) {
	$numbers = [];
	$last = 0;
	foreach (str_split($input) as $j => $v) {
		$numbers [] = ['size' => intval($v),'value' => $j % 2 === 0 ? $last++ : null];
	}

	$i = count($numbers) - 1;
	while ($i > 0) {
		if (is_null($numbers[$i]['value'])) {
			$i--;
			continue;
		}

		for ($j=0; $j < $i; $j++) {
			if (!is_null($numbers[$j]['value'])) {
				continue;
			}

			if ($numbers[$i]['size'] > $numbers[$j]['size']) {
				continue;
			}

			if ($numbers[$i]['size'] === $numbers[$j]['size']) {
				$numbers[$j]['value'] = $numbers[$i]['value'];
				$numbers[$i]['value'] = null;
				$i--;
				break;
			}

			$diff = $numbers[$j]['size'] - $numbers[$i]['size'];
			array_splice($numbers, $j, 1, [$numbers[$i], ['size' => $diff, 'value' => null]]);
			$numbers[$i+1]['value'] = null;
			break;
		}

		$i--;
	}

	$sum = 0;
	$pos_index = 0;
	foreach ($numbers as $i => $nr) {
		if ($nr['value'] === null || $nr['value'] === 0) {
			$pos_index += $nr['size'];
			continue;
		}

		for ($j=0; $j < $nr['size']; $j++) {
			$sum += $pos_index++ * $nr['value'];
		}
	}

	return $sum;
}

$s = microtime(true);

$p = microtime(true);
println('1) Result of demo: ' . part1($input_demo));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('2) Result of demo: ' . part2($input_demo));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
