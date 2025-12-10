<?php

use Adamkiss\Toolkit\A;
use Adamkiss\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
[.##.] (3) (1,3) (2) (2,3) (0,2) (0,1) {3,5,4,7}
[...#.] (0,2,3,4) (2,3) (0,4) (0,1,2) (1,2,3,4) {7,5,12,7,2}
[.###.#] (0,1,2,3,4) (0,3,4) (0,1,2,4,5) (1,2) {10,11,11,5,10,5}
INPUT;

class Machine {
	public int $target = -1;
	public array $buttons = [];
	public array $joltages = [];

	public function __construct(string $s) {
		$parts = Str::split(trim($s), " ");
		$target = trim(array_shift($parts), '[]');
		$joltages = trim(array_pop($parts), '{}');

		$target = Str::replace($target, ['.', '#'], ['0', '1']);
		$this->target = bindec($target);
		$maxbits = strlen($target);

		$this->joltages = A::map(Str::split($joltages), intval(...));

		foreach ($parts as $bstr) {
			$bits = Str::split(trim($bstr, '()'));
			$button = 0;
			foreach ($bits as $bit) {
				$button |= 1 << (($maxbits-1) - intval($bit));
			}
			$this->buttons[] = $button;
		}
	}
}

/**
 * @param array<int, Machine> $machines
 * @return void
 */
function part1 (array $machines) {
	$total = 0;

	foreach ($machines as $m) {
		$q = new SplPriorityQueue();
		$q->setExtractFlags($q::EXTR_BOTH);
		foreach ($m->buttons as $i => $b) {
			$q->insert([0 ^ $b, [$i]], 100_000);
		}

		while (!$q->isEmpty()) {
			['data' => [$v, $steps], 'priority' => $p] = $q->extract();
			$p--;
			foreach ($m->buttons as $i => $b) {
				$nv = $v ^ $b;
				$nsteps = A::merge($steps, [$i]);
				// printf("%d in steps %s\n", $nv, join(",", $nsteps));
				if ($nv === $m->target) {
					$total += count($nsteps);
					break 2;

				}
				$q->insert([$nv, $nsteps], $p);
				// if ($q->count() === 1_000_000) {
				// 	ray($q->extract());
				// 	die('Queue size of 1_000_000 reached, exiting');
				// }
			}
		}
	}

	return $total;
}

function part2 (string $input) {
	return true;
}

$s = microtime(true);

$machines_demo = A::map(Str::split($input_demo, "\n"), fn ($mstr) => new Machine($mstr));
$machines_input = A::map(Str::split($input, "\n"), fn ($mstr) => new Machine($mstr));

printf("0) Parsing » %.3fms\n", (microtime(true)-$s) * 1000);

// 1
$p = microtime(true);
$r = part1($machines_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 7);

$p = microtime(true);
println('1) Result of real input: ' . part1($machines_input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
// $p = microtime(true);
// $r = part2($input_demo);
// println('2) Result of demo: ' . $r);
// printf("» %.3fms\n", (microtime(true)-$p) * 1000);
// assert($r === 1);
//
// $p = microtime(true);
// println('2) Result of real input: ' . part2($input));
// printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
