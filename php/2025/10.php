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
	public array $buttons_raw = [];
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
			$this->buttons_raw [] = A::map($bits, intval(...));
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

	/*
	 * EXAMPLE
	 * FROM
	 * [.###.#] (0,1,2,3,4) (0,3,4) (0,1,2,4,5) (1,2) {10,11,11,5,10,5}
	 *
	 * INPUT:
	 * min: +C1 +C2 +C3 +C4;
	 * +C1 +C2 +C3 = 10;
	 * +C1 +C3 +C4 = 11;
	 * +C1 +C3 +C4 = 11;
	 * +C1 +C2 = 5;
	 * +C1 +C2 +C3 = 10;
	 * +C3 = 5;
	 * C1 <= 200;
	 * C2 <= 200;
	 * C3 <= 200;
	 * C4 <= 200;
	 * int C1,C2,C3,C4;
	 */
	public function match_joltages_steps(): int {
		$input_cs = [];
		$input_buttons = [];
		for ($i = 1; $i <= max(count($this->joltages), count($this->buttons)); $i++) {
			$input_cs [] = "C{$i}";
		}
		foreach ($this->joltages as $i => $j) {
			$input_buttons[$i] = [];
		}
		foreach ($this->buttons_raw as $i => $b) {
			foreach ($b as $_ => $register) {
				$input_buttons[$register][] = $i + 1;
			}
		}
		foreach ($input_buttons as $i => $ib) {
			if (empty($ib)) {
				rd($input_buttons);
			}
		}

		$input = sprintf(
			<<<INPUT
			min: +%s;
			%s
			%s
			int %s;
			INPUT,
			A::join($input_cs, ' +'),
			A::join(array_map(function ($b, $i) {
				return sprintf(
					'%s+C%s = %d;',
					count($b) <= 1 ? 'R' . ($i + 1) . ': ' : '',
					A::join($b, ' +C'),
					$this->joltages[$i]
				);
			}, $input_buttons, array_keys($input_buttons)), "\n"),
			A::join(A::map($input_cs, fn ($c) => "{$c} <= 200;"), "\n"),
			A::join($input_cs, ',')
		);
		$output = shell_exec("echo \"$input\" | lp_solve -S2");
		// println($input, $output);
		$cs = Str::matchAll($output, '/^C(\d+)\s*(\d+)$/m', );
		$output = array_sum(A::map($cs[2] ?? [], intval(...)));
		return $output;
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

function part2(array $machines) {
	$total = 0;
	foreach ($machines as $i => $m) {
		$sub = $m->match_joltages_steps();
		// println("$i $sub");
		// if ($i === 4) {
		// 	die();
		// }
		$total += $sub;
	}
	// $total = array_reduce($machines, fn ($c, $m) => $c + $m->match_joltages_steps(), 0);
	return $total;
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
$p = microtime(true);
$r = part2($machines_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true) - $p) * 1000);
assert($r === 33);

$p = microtime(true);
println('2) Result of real input: ' . part2($machines_input));
printf("» %.3fms\n", (microtime(true) - $p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true) - $s) * 1000);
