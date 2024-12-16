<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set('memory_limit', '15000M');

$input = read_input();
$input_demo = <<<INPUT
###############
#.......#....E#
#.#.###.#.###.#
#.....#.#...#.#
#.###.#####.#.#
#.#.#.......#.#
#.#.#####.###.#
#...........#.#
###.#.#####.#.#
#...#.....#.#.#
#.#.#.###.#.#.#
#.....#...#.#.#
#.###.#.#.#.#.#
#S..#.....#...#
###############
INPUT;
$input_demo2 = <<<INPUT
#################
#...#...#...#..E#
#.#.#.#.#.#.#.#.#
#.#.#.#...#...#.#
#.#.#.#.###.#.#.#
#...#.#.#.....#.#
#.#.#.#.#.#####.#
#.#...#.#.#.....#
#.#.#####.#.###.#
#.#.#.......#...#
#.#.###.#####.###
#.#.#...#.....#.#
#.#.#.#####.###.#
#.#.#.........#.#
#.#.#.#########.#
#S#.............#
#################
INPUT;

const DIRS_VEC = [
	'>' => [1, 0],
	'v' => [0, 1],
	'<' => [-1, 0],
	'^' => [0, -1],
];
const DIRS_RIGHT = [
	'>' => 'v',
	'v' => '<',
	'<' => '^',
	'^' => '>',
];
const DIRS_LEFT = [
	'>' => '^',
	'v' => '>',
	'<' => 'v',
	'^' => '<',
];

function print_map_raw(array $map, array $path): string {
	foreach ($path as [$v, $d]) {
		$map[$v[1]][$v[0]] = $d;
	}
	return join(array: $map, separator: "\n");
}
function print_map(array $map, array $path) {
	println(print_map_raw($map, $path));
}

function vec_add(array $v1, array $v2): array {
	return [$v1[0] + $v2[0], $v1[1] + $v2[1]];
}
function vec_to_str(array $v): string {
	return "{$v[0]};{$v[1]}";
}
function pd_to_str(array $p, string $d): string {
	return vec_to_str($p).";{$d}";
}

function peek(array $lines, array $w) : string|null {
	return $lines[$w[1]][$w[0]] ?? null;
}

function part1 (string $input) {
	$lines = explode("\n", $input);

	for ($i=count($lines)-1; $i >= 0; $i--) {
		match (true) {
			strpos($lines[$i], 'S') !== false => $p = [strpos($lines[$i], 'S'), $i],
			strpos($lines[$i], 'E') !== false => $e = [strpos($lines[$i], 'E'), $i],
			true => null
		};
	}
	$d = '>';

	$visited = [];
	$visited_end = [];
	$q = new SplPriorityQueue();
	$q->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
	$q->insert([$p, $d, []], 0);
	$bestscore = PHP_INT_MAX;

	foreach ($q as ['data' => $v, 'priority' => $prio]) {
		[$p, $d, $path] = $v;
		$score = -$prio;

		$path []= [$p, $d];
		$visited[pd_to_str($p,$d)] = true;

		if ($p === $e) {
			if ($score > $bestscore) {
				return [$bestscore, $visited_end];
			}

			foreach ($path as [$p, $d]) {
				$visited_end[vec_to_str($p)] = true;
			}
			$bestscore = $score;
			continue;
		}

		$l = DIRS_LEFT[$d];
		$r = DIRS_RIGHT[$d];
		foreach ([$d, $l, $r] as $step) {
			$to = vec_add($p, DIRS_VEC[$step]);
			if ($lines[$to[1]][$to[0]] === '#' || isset($visited[pd_to_str($to, $step)])) {
				continue;
			}

			$q->insert([$to, $step, $path], $prio - ($d === $step ? 1 : 1001));
		}
	}
}

function part2 (array $input) {
	return count($input);
}

$s = microtime(true);

// 1
$p = microtime(true);
[$r, $path_demo] = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 7036);

$p = microtime(true);
[$r, $path_demo2] = part1($input_demo2);
println('1) Result of demo2: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 11048);

$p = microtime(true);
[$r, $path_input] = part1($input);
println('1) Result of real input: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($path_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 45);

$p = microtime(true);
$r = part2($path_demo2);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 64);

$p = microtime(true);
println('2) Result of real input: ' . part2($path_input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
