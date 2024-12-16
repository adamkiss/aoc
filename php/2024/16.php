<?php

require_once __DIR__ . '/vendor/autoload.php';

ini_set('memory_limit', '8192M');

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
const DIRS_NEXT = [
	'>' => 'v',
	'v' => '<',
	'<' => '^',
	'^' => '>',
];

class State {
	public int $score = 0;
	public bool $done = false;
	public array $path = [];

	public function __construct(
		public array $p,
		public string $d
	) {}

}

function vec_add(array $v1, array $v2): array {
	return [$v1[0] + $v2[0], $v1[1] + $v2[1]];
}
function vec_to_str(array $v): string {
	return "{{$v[0]};{$v[1]}}";
}

function peek(array $lines, array $w) : string|null {
	return $lines[$w[1]][$w[0]] ?? null;
}

function p1_next(array $lines, State $s) : State|array|false {
	$next = [];
	$score = [0, 1000, 2000, 1000];

	for ($i=0; $i < 4; $i++) {
		$np = vec_add($s->p, DIRS_VEC[$s->d]);
		$n = peek($lines, $np);

		if ($n === 'E') {
			$s->path [vec_to_str($s->p)] = true;
			$s->path [vec_to_str($np)] = true;
			$s->score += 1;
			$s->done = true;
			return $s;
		}

		if (isset($s->path[vec_to_str($np)]) || $n === 'S' || $n === '#') {
			$s->d = DIRS_NEXT[$s->d];
			continue;
		}

		$sn = clone $s;
		$sn->path [vec_to_str($s->p)]= true;
		$sn->p = vec_add($sn->p, DIRS_VEC[$s->d]);
		$sn->score += $score[$i] + 1;
		$next [] = $sn;

		$s->d = DIRS_NEXT[$s->d];
	}

	if (empty($next)) {
		return false;
	}
	return $next;
}

function part1 (string $input) {
	$lines = explode("\n", $input);

	for ($i=count($lines)-1; $i >= 0; $i--) {
		if (!str_contains($lines[$i], 'S')) {
			continue;
		}
		$p = [strpos($lines[$i], 'S'), $i];
		break;
	}
	$d = '>';

	$found = [];
	$states = new SplQueue();
	$states->enqueue(new State($p, $d));

	$iter = 0;

	while ($states->count() > 0) {
		if ($iter++ % 1000 === 0) {
			println($iter, $states->count(), count($found), sprintf("%.3fMB", memory_get_usage() / 1024 / 1024));
		}
		if ($iter % 100000 === 0) {
			var_dump($states);
		}
		$s = $states->dequeue();

		$sa = p1_next($lines, $s);

		if ($sa === false) {
			continue;
		}
		if ($sa instanceof State) {
			if ($sa->done) {
				$found []= $sa;
				continue;
			}
			$states->enqueue($sa);
			continue;
		}
		if (is_array($sa)) {
			foreach ($sa as $i) {
				$states->enqueue($i);
			}
		}

	}

	usort($found, fn ($a, $b) => $a->score <=> $b->score);

	return $found[0]->score;
}

function part2 (string $input) {
	return true;
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 7036);

$p = microtime(true);
$r = part1($input_demo2);
println('1) Result of demo2: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 11048);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 1);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
