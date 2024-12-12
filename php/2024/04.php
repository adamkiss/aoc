<?php

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = A::map(Str::split(read_input(), "\n"), fn($line) => str_split($line));
$input_demo = A::map(Str::split(<<<INPUT
MMMSXXMASM
MSAMXMSMSA
AMXSXMAAMM
MSAMASMSMX
XMASAMXAMM
XXAMMXXAMA
SMSMSASXSS
SAXAMASAAA
MAMMMXMMMM
MXMXAXMASX
INPUT, "\n"), fn($line) => str_split($line));


function part1 (array $map) {
	$max_width = count($map[0]) - 1;
	$max_height = count($map) - 1;
	$found = 0;

	foreach ($map as $y => $line) {
		foreach ($line as $x => $char) {
			if ($char !== 'X') {
				continue;
			}

			if ( //LEFT
				$x >= 3
				&& $map[$y][$x-1] === 'M'
				&& $map[$y][$x-2] === 'A'
				&& $map[$y][$x-3] === 'S'
			) {
				$found += 1;
			}
			if ( //TOP
				$y >= 3
				&& $map[$y-1][$x] === 'M'
				&& $map[$y-2][$x] === 'A'
				&& $map[$y-3][$x] === 'S'
			) {
				$found += 1;
			}
			if ( //RIGHT
				$x <= $max_width - 3
				&& $map[$y][$x+1] === 'M'
				&& $map[$y][$x+2] === 'A'
				&& $map[$y][$x+3] === 'S'
			) {
				$found += 1;
			}
			if ( //DOWN
				$y <= $max_height - 3
				&& $map[$y+1][$x] === 'M'
				&& $map[$y+2][$x] === 'A'
				&& $map[$y+3][$x] === 'S'
			) {
				$found += 1;
			}
			if ( //X LEFT UP
				$x >= 3 && $y >= 3
				&& $map[$y-1][$x-1] === 'M'
				&& $map[$y-2][$x-2] === 'A'
				&& $map[$y-3][$x-3] === 'S'
			) {
				$found += 1;
			}
			if ( //X RIGHT UP
				$x <= $max_width - 3 && $y >= 3
				&& $map[$y-1][$x+1] === 'M'
				&& $map[$y-2][$x+2] === 'A'
				&& $map[$y-3][$x+3] === 'S'
			) {
				$found += 1;
			}
			if ( //X RIGHT DOWN
				$x <= $max_width - 3 && $y <= $max_height - 3
				&& $map[$y+1][$x+1] === 'M'
				&& $map[$y+2][$x+2] === 'A'
				&& $map[$y+3][$x+3] === 'S'
			) {
				$found += 1;
			}
			if ( //X LEFT DOWN
				$x >= 3 && $y <= $max_height - 3
				&& $map[$y+1][$x-1] === 'M'
				&& $map[$y+2][$x-2] === 'A'
				&& $map[$y+3][$x-3] === 'S'
			) {
				$found += 1;
			}
		}
	}

	return $found;
}

function part2 (array $map) {
	$max_width = count($map[0]) - 1;
	$max_height = count($map) - 1;
	$found = 0;

	foreach ($map as $y => $line) {
		foreach ($line as $x => $char) {
			if ($char !== 'A' || $x < 1 || $y < 1 || $x > $max_width-1 || $y > $max_height-1) {
				continue;
			}

			if ( //LEFT MS
				   $map[$y+1][$x-1] === 'M'
				&& $map[$y-1][$x-1] === 'M'
				&& $map[$y-1][$x+1] === 'S'
				&& $map[$y+1][$x+1] === 'S'
			) {
				$found += 1;
			}
			if ( //TOP MS
				   $map[$y+1][$x-1] === 'S'
				&& $map[$y-1][$x-1] === 'M'
				&& $map[$y-1][$x+1] === 'M'
				&& $map[$y+1][$x+1] === 'S'
			) {
				$found += 1;
			}
			if ( //RIGHT MS
				   $map[$y+1][$x-1] === 'S'
				&& $map[$y-1][$x-1] === 'S'
				&& $map[$y-1][$x+1] === 'M'
				&& $map[$y+1][$x+1] === 'M'
			) {
				$found += 1;
			}
			if ( //DOWN MS
				   $map[$y+1][$x-1] === 'M'
				&& $map[$y-1][$x-1] === 'S'
				&& $map[$y-1][$x+1] === 'S'
				&& $map[$y+1][$x+1] === 'M'
			) {
				$found += 1;
			}
		}
	}

	return $found;
}

// PART 1
println('1) Result of demo: ' . part1($input_demo));
println('1) Result of real input: ' . part1($input));
println('–––');
// PART 2
println('2) Result of demo: ' . part2($input_demo));
println('2) Result of real input: ' . part2($input));
