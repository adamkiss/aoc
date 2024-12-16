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
	$visited_all_best = [];
	$q = new SplPriorityQueue();
	$q->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
	$q->insert([$p, $d, []], 0);
	$bs = PHP_INT_MAX;

	foreach ($q as ['data' => $try, 'priority' => $prio]) {
		[$p, $d, $path] = $try;

		$path[] = "{$p[0]};{$p[1]}";
		$visited["{$p[0]};{$p[1]};{$d}"] = true;

		if ($p === $e) {
			if (-$prio > $bs) {
				return [$bs, $visited_all_best];
			}

			$bs = -$prio;
			foreach ($path as $v) {
				$visited_all_best[$v] = true;
			}
			continue;
		}

		$l = DIRS_LEFT[$d];
		$r = DIRS_RIGHT[$d];
		foreach ([$d, $l, $r] as $nd) {
			// $to = [$p[0]+DIRS_VEC[$nd][0],$p[1]+DIRS_VEC[$nd][1]];
			$to = vec_add($p, DIRS_VEC[$nd]);
			if ($lines[$to[1]][$to[0]] === '#' || isset($visited["{$to[0]};{$to[1]};{$nd}"])) {
				continue;
			}

			$q->insert([$to, $nd, $path], $prio - ($d === $nd ? 1 : 1001));
		}
	}
}

function part1_anim (string $input) {
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
	$visited_all_best = [];
	$q = new SplPriorityQueue();
	$q->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
	$q->insert([$p, $d, []], 0);
	$bs = PHP_INT_MAX;

	$animap = [];
	foreach ($lines as $l) {
		$animap [] = str_replace(['#', '.'], ['+', ' '], $l);
	}

	foreach ($q as ['data' => $try, 'priority' => $prio]) {
		print_map($animap, []);
		fwrite(STDOUT, "\e[".count($lines)."A"); // up
		fwrite(STDOUT, "\e[".strlen($lines[0])."D"); // left
		usleep(10_000);

		[$p, $d, $path] = $try;

		$animap[$p[1]][$p[0]] = '#';
		$path[] = "{$p[0]};{$p[1]}";
		$visited["{$p[0]};{$p[1]};{$d}"] = true;

		if ($p === $e) {
			if (-$prio > $bs) {
				return [$bs, $visited_all_best];
			}

			$bs = -$prio;
			foreach ($path as $v) {
				$visited_all_best[$v] = true;
			}
			continue;
		}

		$l = DIRS_LEFT[$d];
		$r = DIRS_RIGHT[$d];
		foreach ([$d, $l, $r] as $nd) {
			$to = vec_add($p, DIRS_VEC[$nd]);
			if ($lines[$to[1]][$to[0]] === '#' || isset($visited["{$to[0]};{$to[1]};{$nd}"])) {
				continue;
			}

			$q->insert([$to, $nd, $path], $prio - ($d === $nd ? 1 : 1001));
		}
	}
}
function xytoi(array $xy, $w): int {
	// +1 = "\n"
	return $xy[0] + $xy[1] * ($w + 1);
}
function itoxy(int $i, int $w): array {
	return [$i % ($w + 1), (int)ceil($i / ($w + 1)) - 1];
}
function part1_string (string $input) {
	$w = strpos($input, "\n");
	$s = itoxy(strpos($input, 'S'), $w);
	$e = itoxy(strpos($input, 'E'), $w);
	$d = '>';

	$visited = [];
	$visited_all_best = [];
	$q = new SplPriorityQueue();
	$q->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
	$q->insert([$s, $d, []], 0);
	$bs = PHP_INT_MAX;

	foreach ($q as ['data' => $try, 'priority' => $prio]) {
		[$s, $d, $path] = $try;

		$path[] = "{$s[0]};{$s[1]}";
		$visited["{$s[0]};{$s[1]};{$d}"] = true;

		if ($s === $e) {
			if (-$prio > $bs) {
				return [$bs, $visited_all_best];
			}

			$bs = -$prio;
			foreach ($path as $v) {
				$visited_all_best[$v] = true;
			}
			continue;
		}

		$l = DIRS_LEFT[$d];
		$r = DIRS_RIGHT[$d];
		foreach ([$d, $l, $r] as $nd) {
			$to = vec_add($s, DIRS_VEC[$nd]);
			if ($input[xytoi($to, $w)] === '#' || isset($visited["{$to[0]};{$to[1]};{$nd}"])) {
				continue;
			}

			$q->insert([$to, $nd, $path], $prio - ($d === $nd ? 1 : 1001));
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
