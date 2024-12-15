<?php

use Ds\Set;
use Kirby\Toolkit\A;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input('15.txt');
$input_demo = <<<INPUT
##########
#..O..O.O#
#......O.#
#.OO..O.O#
#..O@..O.#
#O#..O...#
#O..O..O.#
#.OO.O.OO#
#....O...#
##########

<vv>^<v^>v>^vv^v>v<>v^v<v<^vv<<<^><<><>>v<vvv<>^v^>^<<<><<v<<<v^vv^v>^
vvv<<^>^v^^><<>>><>^<<><^vv^^<>vvv<>><^^v>^>vv<>v<<<<v<^v>^<^^>>>^<v<v
><>vv>v^v^<>><>>>><^^>vv>v<^^^>>v^v^<^^>v^^>v^<^v>v<>>v^v^<v>v^^<^^vv<
<<v<^>>^^^^>>>v^<>vvv^><v<<<>^^^vv^<vvv>^>v<^^^^v<>^>vvvv><>>v^<<^^^^^
^><^><>>><>^^<<^^v>>><^<v>^<vv>>v>>>^v><>^v><<<<v>>v<v<v>vvv>^<><<>^><
^>><>^v<><^vvv<^^<><v<<<<<><^v<<<><<<^^<v<^^^><^>>^<v^><<<^>>^v<v^v<v^
>^>>^v>vv>^<<^v<>><<><<v<<v><>v<^vv<<<>^^v^>^^>>><<^v>>v^v><^^>>^<>vv^
<><^^>^^^<><vvvvv^v<v<<>^v<v>v<<^><<><<><<<^^<<<^<<>><<><^^^>^^<>^>v<>
^^>vv<^v^v<vv>^<><v<^v>^^^>>>^^vvv^>vvv<>>>^<^>>>>>^<<^v>^vvv<>^<><<v>
v^^>>><<^^<>>^v^<v^vv<>v^<<>^<^v^v><^<<<><<^<v><v<>vv>>v><v^<vv<>v^<<^
INPUT;
$input_small = <<<INPUT
########
#..O.O.#
##@.O..#
#...O..#
#.#.O..#
#...O..#
#......#
########

<^^>>>vv<v>>v<<
INPUT;

const DIRS = [
	'>' => new Vec2(1, 0),
	'<' => new Vec2(-1, 0),
	'v' => new Vec2(0, 1),
	'^' => new Vec2(0, -1)
];

function print_map_raw(array $map, ?int $x = null, ?int $y = null): string {
	if (!is_null($x) && !is_null($y)) {
		$map[$y][$x] = 'X';
	}
	return A::join(A::map($map, fn ($line) => A::join($line, '')), "\n");
}

function print_map_ray(array $map, ?int $x = null, ?int $y = null) {
	ray()->html('<pre>'.print_map_raw($map, $x, $y).'</pre>');
}

function print_map(array $map, ?int $x = null, ?int $y = null) {
	print(print_map_raw($map, $x, $y));
	println();
	println();
}


function process_input(string $input): array {
	[$map_raw, $moves_raw] = explode("\n\n", $input);
	$map = array_map(
		array: explode("\n", $map_raw),
		callback: fn($line) => str_split($line)
	);
	$moves = str_split(str_replace("\n", '', $moves_raw));
	return [$map, $moves];
}

function process_input2(string $input): array {
	[$map_raw, $moves_raw] = explode("\n\n", $input);
	$map = array_map(
		array: explode("\n", $map_raw),
		callback: fn($line) => str_split(str_replace(
			['#', 'O', '.', '@'],
			['##', '[]', '..', '@.'],
			$line
		))
	);
	$moves = str_split(str_replace("\n", '', $moves_raw));
	return [$map, $moves];
}

function move_simple(array &$map, Vec2 $pos, Vec2 $d, int $i) : bool {
	$n = $pos->cl_a($d);
	if ($map[$n->y][$n->x] === '#') {
		return false;
	}
	$canmove = in_array($map[$n->y][$n->x], ['O', '[', ']'])
		? move_simple($map, $n, $d, $i+1)
		: true;

	if (! $canmove) {
		return false;
	}

	$map[$n->y][$n->x] = $map[$pos->y][$pos->x];
	return true;
}

function part1 (string $input) {
	[$map, $moves] = process_input($input);

	$h = count($map);
	$w = count($map[0]);

	for ($i=0; $i < $h; $i++) {
		for ($j=0; $j < $w; $j++) {
			if ($map[$i][$j] !== '@') {
				continue;
			}
			$b = new Vec2($j, $i);
			break 2;
		}
	}

	foreach ($moves as $move) {
		$moved = move_simple($map, $b, DIRS[$move], 0);
		if ($moved) {
			$map[$b->y][$b->x] = '.';
			$b->add(DIRS[$move]);
		}
	}

	$sum = 0;

	for ($i=0; $i < $h; $i++) {
		for ($j=0; $j < $w; $j++) {
			if ($map[$i][$j] !== 'O') {
				continue;
			}
			$sum += $i * 100 + $j;
		}
	}

	return $sum;
}


function move_p2v(array $map, Vec2 $pos, $d, int $i) : array|bool {
	$n = $pos->cl_a($d);
	$ch = $map[$n->y][$n->x];
	if ($ch === '#') {
		return false;
	}

	if ($ch === '.') {
		return true;
	}

	$fhalf = move_p2v($map, $n, $d, $i + 1);
	if ($fhalf === false) {
		return false;
	}

	$shalf = ($ch === ']')
		? move_p2v($map, $n->cl_ai(-1, 0), $d, $i + 1)
		: move_p2v($map, $n->cl_ai(+1, 0), $d, $i + 1);

	if ($fhalf === false || $shalf === false) {
		return false;
	}

	$self = $ch === ']'
		? [$n->addi(-1, 0)]
		: [$n];

	if ($shalf === true && $fhalf === true) {
		return $self;
	}

	return array_merge(is_array($fhalf) ? $fhalf : [], is_array($shalf) ? $shalf : [], $self);
}

function part2 (string $input) {
	[$map, $moves] = process_input2($input);

	$h = count($map);
	$w = count($map[0]);

	for ($i=0; $i < $h; $i++) {
		for ($j=0; $j < $w; $j++) {
			if ($map[$i][$j] !== '@') {
				continue;
			}
			$b = new Vec2($j, $i);
			break 2;
		}
	}

	foreach ($moves as $move) {
		$d = DIRS[$move];

		if ($move === '<' || $move === '>') {
			$moved = move_simple($map, $b, $d, 0);
			if ($moved) {
				$map[$b->y][$b->x] = '.';
				$b->add($d);
			}
			continue;
		}

		$to_move = move_p2v($map, $b, $d, 0);

		if ($to_move === false) {
			continue;
		}

		if ($to_move !== true) {
			$unique = (new Set($to_move))->toArray();
			foreach ($unique as $box) {
				$map[$box->y][$box->x+0] = '.';
				$map[$box->y][$box->x+1] = '.';
				$box->add($d);
				$map[$box->y][$box->x+0] = '[';
				$map[$box->y][$box->x+1] = ']';
			}
		}

		$map[$b->y][$b->x] = '.';
		$b->add($d);
		$map[$b->y][$b->x] = '@';
	}

	// print_map($map);

	$sum = 0;
	for ($i=0; $i < $h; $i++) {
		for ($j=0; $j < $w; $j++) {
			if ($map[$i][$j] !== '[') {
				continue;
			}
			$sum += $i * 100 + $j;
		}
	}

	return $sum;
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_small);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 2028);

$r = part1($input_demo);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 10092);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($input_demo);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 9021);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
