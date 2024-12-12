<?php

require_once __DIR__ . '/vendor/autoload.php';
$input = trim(file_get_contents(__DIR__ . '/inputs/' . basename(__FILE__, 'php') . 'txt'));
$dinput = <<<INPUT
125 17
INPUT;

function setOrIncrease(&$arr, $i, $v = 1) : void {
	$arr[$i] = isset($arr[$i]) ? $arr[$i] + $v : $v;
}

function partKeys (string $input, int $blinks = 22): int {
	$initial = array_map('intval', explode(' ', $input));
	$n = [];
	foreach ($initial as $v) {
		setOrIncrease($n, $v);
	}

	for ($i=0; $i < $blinks; $i++) {
		$n2 = [];
		foreach ($n as $v => $c) {
			if ($v === 0) {
				setOrIncrease($n2, 1, $c);
			} else if (strlen((string)$v) % 2 === 0) {
				$v = (string) $v;
				$l = round(strlen($v) / 2);
				setOrIncrease($n2, intval(substr($v, 0, $l), 10), $c);
				setOrIncrease($n2, intval(substr($v, $l), 10), $c);
			} else {
				setOrIncrease($n2, $v * 2024, $c);
			}
		}
		$n = $n2;
	}
	return array_sum($n);
}

ray()->measure();
$s = microtime(true);

// 1
$p = microtime(true);
$r = partKeys($dinput, 6);
println('1) Result of demo (6): ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 22);

$p = microtime(true);
$r = partKeys($dinput, 25);
println('1) Result of demo2 (25): ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 55312);

$p = microtime(true);
println('1) Result of real input: ' . partKeys($input, 25));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

println();

// 2
$p = microtime(true);
println('2) Result of real input: ' . partKeys($input, 75));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

println();
printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
ray()->measure();
