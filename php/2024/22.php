<?php


require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
1
10
100
2024
INPUT;
$input_demo2 = <<<INPUT
1
2
3
2024
INPUT;

function step(int $n): int {
	$_ = $n * 64;
	$n ^= $_;
	$n = $n % 16777216;
	$_ = (int)floor($n / 32);
	$n ^= $_;
	$n = $n % 16777216;
	$_ = $n * 2048;
	$n ^= $_;
	$n = $n % 16777216;
	return $n;
}

function process_input(string $i): array {
	return array_map('intval', explode("\n", $i));
}

function part1(string $input) {
	$nums = process_input($input);
	$sum = 0;
	foreach ($nums as $n) {
		for ($i = 0; $i < 2000; $i++) {
			$n = step($n);
		}
		$sum += $n;
	}
	return $sum;
}

function part2_debug() {
	$max = [];
	foreach ([123] as $numord => $on) {
		$seen = [];

		$price = $on % 10;
		$d = [[$price, null]];
		$n = $on;

		for ($i = 1; $i <= 10; $i++) {
			$n = step($n);
			$nextPrice = $n % 10;
			$d [] = [$nextPrice, $nextPrice - $price];
			$price = $nextPrice;

			if ($i < 4) {
				continue;
			}
			$seq = join(',', [
				$d[$i - 3][1],
				$d[$i - 2][1],
				$d[$i - 1][1],
				$d[$i - 0][1],
			]);
			println($i, $seq, $price, count($d));
			if (isset($seen[$seq])) {
				continue;
			}
			$seen[$seq] = true;
			if (!isset($max[$seq])) {
				$max[$seq] = 0;
			}
			$max[$seq] += $price;
		}
	}
	rd($d);
}

function part2(string $input) {
	$nums = process_input($input);
	$max = [];
	foreach ($nums as $numord => $on) {
		$seen = [];
		$n = $on;
		$price = $n % 10;
		$d = [[$price, null]];

		for ($i = 1; $i <= 2000; $i++) {
			$n = step($n);
			$nextPrice = $n % 10;
			$d [] = [$nextPrice, $nextPrice - $price];
			$price = $nextPrice;

			if ($i < 3) {
				continue;
			}
			$seq = join(',', [
				$d[$i - 3][1],
				$d[$i - 2][1],
				$d[$i - 1][1],
				$d[$i - 0][1],
			]);
			if (isset($seen[$seq])) {
				continue;
			}
			$seen[$seq] = true;
			if (!isset($max[$seq])) {
				$max[$seq] = 0;
			}
			$max[$seq] += $price;
		}
	}
	arsort($max);
	$k = array_keys($max);
	return $max[$k[0]];
}

$s = microtime(true);

$p = microtime(true);
println('TESTING STEP FUNC');
assert(step(123) === 15887950);
assert(step(15887950) === 16495136);
assert(step(16495136) === 527345);
assert(step(527345) === 704524);
assert(step(704524) === 1553684);
assert(step(1553684) === 12683156);
assert(step(12683156) === 11100544);
assert(step(11100544) === 12249484);
assert(step(12249484) === 7753432);
assert(step(7753432) === 5908254);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);


// 1
// $p = microtime(true);
// $r = part1($input_demo);
// println('1) Result of demo: ' . $r);
// printf("» %.3fms\n", (microtime(true)-$p) * 1000);
// assert($r === 37327623);
//
$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true) - $p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo2);
println('2) Result of demo (2): ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r === 23);

$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo (1): ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r === 24);

$p = microtime(true);
$r = part2($input);
println('2) Result of real input: ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r !== 2274); // first guess - missed "use sequence only once p monkey"
assert($r !== 2240); // second guess // too low?

printf("TOTAL: %.3fms\n", (microtime(true) - $s) * 1000);
