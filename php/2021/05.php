<?php

use Kirby\Toolkit\Obj;
use Kirby\Toolkit\Collection;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
0,9 -> 5,9
8,0 -> 0,8
9,4 -> 3,4
2,2 -> 2,1
7,0 -> 7,4
6,4 -> 2,0
0,9 -> 2,9
3,4 -> 1,4
0,0 -> 8,8
5,5 -> 8,2
INPUT;

function process_input(string $i) : Collection {
	return (new Collection(explode("\n", $i)))
		->map(function($lineString) {
			preg_match('/(?P<sx>\d+),(?P<sy>\d+) -> (?P<ex>\d+),(?P<ey>\d+)/', $lineString, $matches);
			return new Obj([
				'original' => $lineString,
				's' => new Obj(['x'=>$matches['sx'],'y'=>$matches['sy']]),
				'e' => new Obj(['x'=>$matches['ex'],'y'=>$matches['ey']])
			]);
		});
}

function part1 (string $input) {
	$map = [];
	$lines = process_input($input)
		->map(function($line) use (&$map) {
			if ($line->s->x !== $line->e->x && $line->s->y !== $line->e->y) return;

			for (
				$i = $line->s->x;
				$line->e->x >= $line->s->x ? $i <= $line->e->x : $i >= $line->e->x;
				$line->e->x >= $line->s->x ? $i++ : $i--
			) {
				for (
					$j = $line->s->y;
					$line->e->y >= $line->s->y ? $j <= $line->e->y : $j >= $line->e->y;
					$line->e->y >= $line->s->y ? $j++ : $j--
				) {
					if (array_key_exists("$i:$j", $map)) {
						$map["$i:$j"] += 1;
					} else {
						$map["$i:$j"] = 1;
					}
				}
			}
		});

	return count(array_filter($map, fn($lines) => $lines > 1));
}

function part2 (string $input) {
	$map = [];
	$lines =
		process_input($input)
		->map(function($line) use (&$map) {
			$count = ($line->e->x-$line->s->x === 0)
				? abs($line->e->y-$line->s->y) // vertical
				: abs($line->e->x-$line->s->x); // horizontal or diagonal

			for ($i = 0; $i <= $count; $i++) {
				$x = $line->s->x + $i * ($line->e->x - $line->s->x) / $count;
				$y = $line->s->y + $i * ($line->e->y - $line->s->y) / $count;
				if (array_key_exists("$x:$y", $map)) {
					$map["$x:$y"] += 1;
				} else {
					$map["$x:$y"] = 1;
				}
			}
		});

	return count(array_filter($map, fn($lines) => $lines > 1));
}

$s = microtime(true);

// 1
$p = microtime(true);
println('1) Result of demo: ' . part1($input_demo));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
println('2) Result of demo: ' . part2($input_demo));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
