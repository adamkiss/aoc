<?php

ini_set('memory_limit', '10240M');

use Ds\Map;
use Kirby\Data\PHP;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
5,4
4,2
4,5
3,0
2,1
6,3
2,4
1,5
0,6
3,3
2,6
5,1
1,2
5,5
2,5
6,5
1,4
0,4
6,4
1,1
6,1
1,0
0,5
1,6
2,0
INPUT;

class MapPos {
	public function __construct(
		public int $x,
		public int $y,
		public bool $corrupted,
		public bool $visited
	) {}
}

function process_input(string $input, int $w, int $h, int $limit = 1024) : array {
	$m = [];
	$c = 1;
	for ($i=0; $i < $h; $i++) {
		$m[$i] = [];
		for ($j=0; $j < $w; $j++) {
			$m[$i][$j] = a(corrupted: false, visited: false);
		}
	}
	foreach (explode("\n", $input) as $p) {
		$pos = array_map(fn ($j) => intval($j), explode(',', $p));
		$m[$pos[1]][$pos[0]]['corrupted'] = true;
		if ($c++ === $limit) {
			break;
		}
	}
	return $m;
}

function print_map(array $m, $w, $h, array $steps = []): string {
	$s = '';
	for ($j=0; $j < $h; $j++) {
		for ($i=0; $i < $w; $i++) {
			$s .= match(true) {
				in_array([$i,$j], $steps) => 'O',
				isset($m[$j][$i]) && $m[$j][$i]['corrupted'] => '#',
				isset($m[$j][$i]) && $m[$j][$i]['visited'] => ' ',
				default => '.'
			};
		}
		$s .= "\n";
	}
	return $s;
}

function find_best(array $m, int $w, int $h) : array {
	$targetx = $w - 1;
	$targety = $h - 1;

	$q = new SplPriorityQueue();
	$q->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
	$q->insert([0,0,[],0], 0);

	$best = [PHP_INT_MAX, []];

	$c = 0;
	while($q->count() > 0) {
		[
			'data' => [$x, $y, $path, $steps],
			'priority' => $distance
		] = $q->extract();

		$c++;

		if ($x === $targetx && $y === $targety) {
			if ($steps > $best[0]) {
				continue;
			}
			$best = [$steps, $path];
			continue;
		}

		if ($x < 0 || $y < 0 || $x >= $w || $y >= $h) {
			continue;
		}
		if ($m[$y][$x]['corrupted'] || $m[$y][$x]['visited']){
			continue;
		}

		$m[$y][$x]['visited'] = true;
		$path[] = [$x, $y];
		$steps++;

		// if ($c++ % 10000 === 0) {
		// 	print(print_map($m, $w, $h));
		// 	printf("%s (%.3fMB) -> %s", $q->count(), memory_get_usage(true) / 1024 / 1024, $distance);
		// 	fwrite(STDOUT, "\e[{$w}A"); // up
		// 	fwrite(STDOUT, "\e[".($h-1)."D"); // left
		// }

		$dist = sqrt(($w - $x) * ($w - $x) + ($h - $y) * ($h - $y));
		foreach ([[0, 1], [1, 0], [-1, 0], [0, -1]] as [$dx, $dy]) {
			[$nx, $ny] = [$x + $dx, $y + $dy];
			$q->insert(
				[$x+$dx, $y+$dy, $path, $steps],
				-$steps + $dist
			);
		}
	}
	$best[2] = $c;
	return $best;
}

function part1 (string $input, int $limit, int $w, int $h) {
	$m = process_input($input, $w, $h, $limit);
	$best = find_best($m, $w, $h);
	return $best[0];
}

function part2 (string $input, $limit, $w, $h) {
	$m = process_input($input, $w, $h, $limit);
	for ($i=0; $i <= $limit; $i++) {
		$input = substr($input, strpos($input, "\n")+1);
	}
	$c = 1;
	foreach (explode("\n", $input) as $next) {
		[$nx, $ny] = array_map('intval', explode(',', $next));
		$m[$ny][$nx]['corrupted'] = true;
		[$steps, $_] = find_best($m, $w, $h);
		if ($steps === PHP_INT_MAX) {
			println("Try #{$c}");
			return "{$nx},{$ny}";
		}
		$c++;
	}
	return 'FAIL';
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo, 12, 7, 7);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 22);

$p = microtime(true);
println('1) Result of real input: ' . part1($input, 1024, 71, 71));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo, 12, 7, 7);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === "6,1");

$p = microtime(true);
println('2) Result of real input: ' . part2($input, 1024, 71, 71));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
