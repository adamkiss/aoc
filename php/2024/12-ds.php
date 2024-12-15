<?php

use Ds\Map;
use Ds\Set;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input('12.txt');
$di = <<<INPUT
RRRRIICCFF
RRRRIICCCF
VVRRRCCFFF
VVRCCCJFFF
VVVVCJJCFE
VVIVCCJJEE
VVIIICJJEE
MIIIIIJJEE
MIIISIJEEE
MMMISSJEEE
INPUT;
$di21 = <<<INPUT
AAAA
BBCD
BBCC
EEEC
INPUT;
$di22 = <<<INPUT
EEEEE
EXXXX
EEEEE
EXXXX
EEEEE
INPUT;
$di23 = <<<INPUT
AAAAAA
AAABBA
AAABBA
ABBAAA
ABBAAA
AAAAAA
INPUT;

const DEBUG = false;
const DIRS = [
	[1, 0], [-1, 0],
	[0, 1], [0, -1]
];

class AocMap {
	public Map $m;
	public int $w;
	public int $h;

	public function __construct(string $input) {
		$this->m = new Map();
		foreach (explode("\n", $input) as $y => $line) {
			$h = $y;
			foreach (str_split($line) as $x => $ch) {
				$this->m[[$x, $y]] = new Pos($ch, $x, $y);
				$w = $x;
			}
		}
		$this->w = $w+1;
		$this->h = $h+1;
	}

	public function has(int $x, int $y) : bool
	{
		return $x >= 0 && $x < $this->w && $y >= 0 && $y < $this->h;
	}

	public function get(int $x, int $y) : Pos|null
	{
		try {
			return $this->m[[$x,$y]];
		} catch (\Throwable $th) {
			return null;
		}
	}

	public function flood(Pos $p): void
	{
		foreach (DIRS as [$dx, $dy]) {
			[$nx, $ny] = vec_add($p->x, $p->y, $dx, $dy);
			if (! $this->has($nx, $ny)) {
				$p->plot->perimeter += 1;
				continue;
			}

			$next = $this->get($nx, $ny);
			if ($next->id !== $p->id) {
				$p->plot->perimeter += 1;
				continue;
			}

			if (!is_null($next->plot)) {
				continue;
			}

			$next->plot = $p->plot;
			$next->plot->size += 1;

			$this->flood($next);
		}
	}

	public function plot2(int $x, int $y) : void
	{
		$pos = $this->get($x, $y);
		$e = '';

		foreach (DIRS as [$dx, $dy]) {
			[$nx, $ny] = vec_add($x, $y, $dx, $dy);
			if (! $this->has($nx, $ny)) {
				$e .= ($dx === 0) ? 'h' : 'v';
				continue;
			}

			$next = $this->get($nx, $ny);
			if ($next->id !== $pos->id) {
				$e .= ($dx === 0) ? 'h' : 'v';
				continue;
			}
			if (!is_null($next->plot)) {
				continue;
			}

			$next->plot = $pos->plot;
			$pos->plot->size += 1;
			$this->plot2($nx, $ny);
		}

		$pos->corners = match(strlen($e)) {
			2 => ($e === 'vv' || $e === 'hh') ? 0 : 1,
			3 => 2,
			4 => 4,
			default => 0
		};
		$pos->plot->convex += $pos->corners;
		if ($pos->corners < 2) {
			$pos->plot->concave->add(...$pos->concave_corners($this));
		}
	}
}

class Pos {
	public function __construct(
		public string $id,
		public int $x,
		public int $y
	) {}

	public ?Plot $plot = null;
	public ?Set $edges = null;
	public int $corners = 0;

	public function concave_corners(AocMap $m) : array
	{
		$x = $this->x;
		$y = $this->y;
		return array_filter([
			$this->id !== $m->get($x-1, $y-1)?->id
				&& $this->id === $m->get($x-1, $y  )?->id
				&& $this->id === $m->get($x  , $y-1)?->id
				? sprintf('%s;%s', $x, $y) : null,
			$this->id !== $m->get($x+1, $y-1)?->id
				&& $this->id === $m->get($x+1, $y  )?->id
				&& $this->id === $m->get($x  , $y-1)?->id
				? sprintf('%s;%s', $x+1, $y) : null,
			$this->id !== $m->get($x+1, $y+1)?->id
				&& $this->id === $m->get($x+1, $y  )?->id
				&& $this->id === $m->get($x  , $y+1)?->id
				? sprintf('%s;%s', $x+1, $y+1) : null,
			$this->id !== $m->get($x-1, $y+1)?->id
				&& $this->id === $m->get($x-1, $y  )?->id
				&& $this->id === $m->get($x  , $y+1)?->id
				? sprintf('%s;%s', $x, $y+1) : null,
		], fn($e)=>$e);
	}
}

class Plot {
	public function __construct(
		public string $id
	) {
		$this->concave = new Set();
	}

	public ?int $size = 1;
	public ?int $perimeter = 0;
	public ?int $convex = 0;
	public Set $concave;

	public function corners() : int
	{
		return $this->convex + $this->concave->count();
	}
}

function vec_sub(int $x1, int $y1, int $x2, int $y2): array {
	return [$x1 - $x2, $y1 - $y2];
}
function vec_add(int $x1, int $y1, int $x2, int $y2): array {
	return [$x1 + $x2, $y1 + $y2];
}

function part1 (string $input) {
	$m = new AocMap($input);
	$plots = new Set();

	for ($y=0; $y < $m->h; $y++) {
		for ($x=0; $x < $m->w; $x++) {
			if ($m->get($x,$y)?->plot) {
				continue;
			}

			$pos = $m->get($x, $y);

			$p = new Plot($m->get($x, $y)->id);
			$plots->add($p);
			$pos->plot = $p;

			$m->flood($pos);
		}
	}

	return $plots->reduce(
		fn($a, Plot $i) => $a + $i->size * $i->perimeter,
		0
	);
}

function part2 (string $input) {
	$m = new AocMap($input);
	$plots = new Set();

	for ($y=0; $y < $m->h; $y++) {
		for ($x=0; $x < $m->w; $x++) {
			if ($m->get($x,$y)->plot) {
				continue;
			}

			$p = new Plot($m->get($x, $y)->id);
			$plots->add($p);
			$m->get($x, $y)->plot = $p;
			$m->plot2($x, $y);
		}
	}

	return $plots->reduce(
		fn($a, Plot $i) => $a + $i->size * $i->corners(),
		0
	);
}

ray()->measure();
$s = microtime(true);

// 1
$p = microtime(true);
println('1) Result of demo: ' . part1($di));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

$p = microtime(true);
println('1) Result of real input: ' . part1($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
$r = part2($di);
println('2) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 1206);

$p = microtime(true);
$r = part2($di21);
println('2) Result of demo 2-1: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 80);

$p = microtime(true);
$r = part2($di22);
println('2) Result of demo 2-2: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 236);

$p = microtime(true);
$r = part2($di23);
println('2) Result of demo 2-3: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 368);

$p = microtime(true);
println('2) Result of real input: ' . part2($input));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
ray()->measure();
