<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input(8);
const TEST_INPUT = <<<INPUT
............
........0...
.....0......
.......0....
....0.......
......A.....
............
............
........A...
.........A..
............
............
INPUT;
const TEST_RESULT1 = 14;
const TEST_RESULT2 = 34;

function createMap(string $input): array {
    $map = [];

    $w = 0;

    $lines = explode("\n", $input);

    foreach ($lines as $y => $line) {
        $chars = str_split($line);
        foreach ($chars as $x => $c) {
            $kind = $c;
            $map[] = [$kind, [$x, $y]];
        }
    }

    return [
        $map,
        strlen($lines[0]),
        count($lines)
    ];
}

function indexMap(array $map): array {
    $index = [];

    foreach ($map as $pos) {
        if ($pos[0] !== '.') {
            $index[$pos[0]][] = $pos[1];
        }
    }

    return $index;
}

function part1(array $index, $w, $h): int {
    $nodes = [];

    foreach ($index as $positions) {
        $len = count($positions);
        for ($i = 0; $i < $len; $i++) {
            for ($j = $i + 1; $j < $len; $j++) {
                $dx = $positions[$j][0] - $positions[$i][0];
                $dy = $positions[$j][1] - $positions[$i][1];
                $ax = $positions[$i][0] - $dx;
                $ay = $positions[$i][1] - $dy;
                $bx = $positions[$j][0] + $dx;
                $by = $positions[$j][1] + $dy;

                if ($ax >= 0 && $ax < $w && $ay >= 0 && $ay < $h) {
                    $nodes[$ax.'.'.$ay] = true;
                }
                if ($bx >= 0 && $bx < $w && $by >= 0 && $by < $h) {
                    $nodes[$bx.'.'.$by] = true;
                }
            }
        }
    }

    return count($nodes);
}

function part2(array $index, $w, $h): int {
    $nodes = [];

    foreach ($index as $positions) {
        $len = count($positions);
        for ($i = 0; $i < $len; $i++) {
            for ($j = $i + 1; $j < $len; $j++) {
                $ax = $positions[$i][0];
                $ay = $positions[$i][1];

                $by = $positions[$j][1];
                $bx = $positions[$j][0];

                $dx = $ax - $bx;
                $dy = $ay - $by;

                while (true) {
                    $ax = $ax - $dx;
                    $ay = $ay - $dy;
                    if ($ax >= 0 && $ax < $w && $ay >= 0 && $ay < $h) {
                        $nodes[$ax.'.'.$ay] = true;
                    } else {
                        break;
                    }
                }

                while (true) {
                    $bx = $bx + $dx;
                    $by = $by + $dy;
                    if ($bx >= 0 && $bx < $w && $by >= 0 && $by < $h) {
                        $nodes[$bx.'.'.$by] = true;
                    } else {
                        break;
                    }
                }
            }
        }
    }

    return count($nodes);
}

[$tmap, $tw, $th] = createMap(TEST_INPUT);
$tindex = indexMap($tmap);
assert(part1($tindex, $tw, $th), TEST_RESULT1);
assert(part2($tindex, $tw, $th), TEST_RESULT2);

$before = microtime(true);
[$map, $w, $h] = createMap($input);
$index = indexMap($map);
$afterIndex = microtime(true);

echo part1($index, $w, $h) . "\n";
echo part2($index, $w, $h) . "\n";
$after = microtime(true);

printf("TOTAL: %.3fms\n", ($after - $before) * 1000);
printf("Indexing: %.3fms\n", ($afterIndex - $before) * 1000);
printf("Calculating: %.3fms\n", ($after - $afterIndex) * 1000);
