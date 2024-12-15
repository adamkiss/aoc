<?php

use Ds\Set;
use Kirby\Toolkit\A;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
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
	'>' => [1, 0],
	'<' => [-1, 0],
	'v' => [0, 1],
	'^' => [0, -1]
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

function vec_add(int $x1, int $y1, int $x2, int $y2): array {
	return [$x1 + $x2, $y1 + $y2];
}

function p1move(array &$map, int $x, int $y, $dir, int $i) : bool {
	[$nx, $ny] = vec_add($x, $y, $dir[0], $dir[1]);
	if ($map[$ny][$nx] === '#') {
		return false;
	}
	$canmove = $map[$ny][$nx] === 'O'
		? p1move($map, $nx, $ny, $dir, $i+1)
		: true;

	if (! $canmove) {
		return false;
	}

	$map[$ny][$nx] = $map[$y][$x];
	return true;
}

function part1 (string $input) {
	[$map, $moves] = process_input($input);

	$h = count($map);
	$w = count($map[0]);

	$bx = -1;
	$by = -1;
	for ($i=0; $i < $h; $i++) {
		for ($j=0; $j < $w; $j++) {
			if ($map[$i][$j] !== '@') {
				continue;
			}
			$bx = $j;
			$by = $i;
			break 2;
		}
	}

	foreach ($moves as $move) {
		$moved = p1move($map, $bx, $by, DIRS[$move], 0);
		if ($moved) {
			$map[$by][$bx] = '.';
			[$bx, $by] = vec_add($bx, $by, DIRS[$move][0], DIRS[$move][1]);
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

function p2moveh(array &$map, int $x, int $y, $dir, int $i) : bool {
	[$nx, $ny] = vec_add($x, $y, $dir[0], $dir[1]);

	if ($map[$ny][$nx] === '#') {
		return false;
	}
	$canmove = ($map[$ny][$nx] === ']' || $map[$ny][$nx] === '[')
		? p2moveh($map, $nx, $ny, $dir, $i+1)
		: true;

	if (! $canmove) {
		return false;
	}

	$map[$ny][$nx] = $map[$y][$x];
	return true;
}

function p2checkv(array $map, int $x, int $y, $dir, int $i) : array|bool {
	[$nx, $ny] = vec_add($x, $y, $dir[0], $dir[1]);
	if ($map[$ny][$nx] === '#') {
		return false;
	}

	if ($map[$ny][$nx] === '.') {
		return true;
	}

	$fhalf = p2checkv($map, $nx, $ny, $dir, $i + 1);
	if ($fhalf === false) {
		return false;
	}

	$shalf = ($map[$ny][$nx] === ']')
		? p2checkv($map, $nx-1, $ny, $dir, $i + 1)
		: p2checkv($map, $nx+1, $ny, $dir, $i + 1);

	if ($fhalf === false || $shalf === false) {
		return false;
	}

	if ($fhalf === true && $shalf === true) {
		return $map[$ny][$nx] === ']'
			? [[$nx-1, $ny]]
			: [[$nx, $ny]]
	}

	return array_unique(array_merge($fhalf, $shalf));
}

function part2 (string $input) {
	[$map, $moves] = process_input2($input);

	$h = count($map);
	$w = count($map[0]);

	$bx = -1;
	$by = -1;
	for ($i=0; $i < $h; $i++) {
		for ($j=0; $j < $w; $j++) {
			if ($map[$i][$j] !== '@') {
				continue;
			}
			$bx = $j;
			$by = $i;
			break 2;
		}
	}

	foreach ($moves as $move) {
		$d = DIRS[$move];
		if ($move === '<' || $move === '>') {
			$moved = p2moveh($map, $bx, $by, $d, 0);
			if ($moved) {
				$map[$by][$bx] = '.';
				[$bx, $by] = vec_add($bx, $by, $d[0], $d[1]);
			}
			continue;
		}

		$canmove = p2checkv($map, $bx, $by, $d, 0);

		if ($canmove === false) {
			continue;
		} else if ($canmove === true) {
			$map[$by][$bx] = '.';
			[$bx, $by] = vec_add($bx, $by, $d[0], $d[1]);
			$map[$by][$bx] = '@';
			continue;
		}

		rd($canmove);
	}

	print_map($map);

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

// $p = microtime(true);
// println('2) Result of real input: ' . part2($input));
// printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
