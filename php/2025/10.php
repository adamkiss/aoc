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
		$parts = Str::split(trim($s), ' ');
		$tarstr = trim(array_shift($parts), '[]');
		$joltstr = trim(array_pop($parts), '{}');

		$target = 0;
		foreach (str_split($tarstr) as $i => $c) {
			if ($c !== '#') {
				continue;
			}
			$target += 1 << $i;
		}
		$this->target = $target;
		$this->joltages = A::map(Str::split($joltstr), intval(...));

		foreach ($parts as $bstr) {
			$bits = Str::split(trim($bstr, '()'));
			$button = 0;
			foreach ($bits as $bit) {
				$button += 1 << (int)$bit;
			}
			$this->buttons[] = $button;
		}
	}

	public function turn_on_steps(): int {
		/** @var array<int, bool> */
		$stepresults = [0 => true];
		$iter = 0;

		while ($iter < 10_000) {
			$iter++;

			/** @var array<int, bool> */
			$nextresults = [];

			foreach ($stepresults as $stepres => $_) {
				foreach ($this->buttons as $i => $b) {
					$nextval = $stepres ^ $b;

					if ($nextval === $this->target) {
						return $iter;
					}

					$nextresults[$nextval] = true;
				}
			}

			$stepresults = $nextresults;
		}

		echo "Didn't reach total for a machine in 10 000 steps, exiting.\n";
		die(1);
	}
}

/**
 * @param array<int, Machine> $machines
 * @return void
 */
function part1(array $machines) {
	$total = array_reduce($machines, fn ($c, $m) => $c + $m->turn_on_steps(), 0);
	return $total;
}

function part2(string $input) {
	return true;
}

$s = microtime(true);

$machines_demo = A::map(Str::split($input_demo, "\n"), fn ($mstr) => new Machine($mstr));
$machines_input = A::map(Str::split($input, "\n"), fn ($mstr) => new Machine($mstr));

printf("0) Parsing » %.3fms\n", (microtime(true) - $s) * 1000);

// 1
$p = microtime(true);
$r = part1($machines_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r === 7);

$p = microtime(true);
println('1) Result of real input: ' . part1($machines_input));
printf("» %.3fms\n", (microtime(true) - $p) * 1000);

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

printf("TOTAL: %.3fms\n", (microtime(true) - $s) * 1000);
