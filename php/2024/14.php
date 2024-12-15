<?php

use claviska\SimpleImage;
use Kirby\Toolkit\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
p=0,4 v=3,-3
p=6,3 v=-1,-3
p=10,3 v=-1,2
p=2,0 v=2,-1
p=0,0 v=1,3
p=3,0 v=-2,-2
p=7,6 v=-1,-3
p=3,0 v=-1,-2
p=9,3 v=2,3
p=7,3 v=-1,2
p=2,4 v=2,-3
p=9,5 v=-3,-3
INPUT;

class Murderbot {
	public function __construct(
		public int $x,
		public int $y,
		public int $sx,
		public int $sy,
		public int $mw,
		public int $mh
	) {}

	function move(int $i = 1) : void {
		$this->x = ($this->x + $i*$this->sx) % $this->mw;
		$this->y = ($this->y + $i*$this->sy) % $this->mh;

		if ($this->x < 0) {
			$this->x += $this->mw;
		}
		if ($this->y < 0) {
			$this->y += $this->mh;
		}
	}

	function q(): int|null {
		$halfw = (int) floor($this->mw / 2);
		$halfh = (int) floor($this->mh / 2);
		if ($this->x === $halfw || $this->y === $halfh) {
			return null;
		}

		return match(true) {
			$this->x < $halfw && $this->y < $halfh => 0,
			$this->x > $halfw && $this->y < $halfh => 1,
			$this->x > $halfw && $this->y > $halfh => 2,
			$this->x < $halfw && $this->y > $halfh => 3,
		};
	}
}

function print_map(array $murderbots, $w, $h) {
	$m = [];
	foreach ($murderbots as $b) {
		if (isset($m[$b->y][$b->x])) {
			$m[$b->y][$b->x] += 1;
		} else {
			$m[$b->y][$b->x] = 1;
		}
	}
	$lx = (int) floor($w / 2);
	$ly = (int) floor($h / 2);
	$s = '';
	for ($i=0; $i < $h; $i++) {
		for ($j=0; $j < $w; $j++) {
			if ($i === $ly || $j === $lx) {
				$s .= ' ';
			} else {
				$s .= isset($m[$i][$j]) ? $m[$i][$j] : '.';
			}
		}
		$s .= "\n";
	}
	println($s);
}

function single_string_map(array $murderbots, $w, $h) {
	$m = [];
	$s = '';
	foreach ($murderbots as $b) {
		if (isset($m[$b->y][$b->x])) {
			$m[$b->y][$b->x] += 1;
		} else {
			$m[$b->y][$b->x] = 1;
		}
	}
	for ($i=0; $i < $h; $i++) {
		for ($j=0; $j < $w; $j++) {
			$s .= isset($m[$i][$j]) ? 'X' : ' ';
		}
	}
	return $s;
}

function has_ten_continuous(array $murderbots, $w, $h) {
	$m = [];
	foreach ($murderbots as $b) {
		$m[$b->y * $w + $b->x] = true;
	}
	ksort($m);

	$c = 0;
	$l = -1;
	foreach (array_keys($m) as $v) {
		$c = ($v === $l + 1) ? $c + 1 : 0;
		if ($c === 10) {
			return true;
		}
		$l = $v;
	}
	return false;
}

function process_input(string $i, int $limitx, int $limity) : array {
	$i = explode("\n", $i);
	foreach ($i as $j=>$b) {
		[,$x,$y,$sx,$sy] = Str::match($b, '/.*=(\d*),(\d*) v=(-?\d*),(-?\d*)/');
		$i[$j] = new Murderbot(intval($x), intval($y), intval($sx), intval($sy), $limitx, $limity);
	}
	return $i;
}

function part1 (string $input, int $mx, int $my) {
	$murderbots = process_input($input, $mx, $my);

	foreach ($murderbots as $i => $mb) {
		$mb->move(100);
	}

	$q = array_reduce(
		$murderbots,
		function($agr, $bot) {
			if (!is_null($q = $bot->q())) {
				$agr[$q]++;
			}
			return $agr;
		},
		[0,0,0,0]
	);
	return array_reduce($q, fn ($a, $i) => $a * $i, 1);
}

function part2_image (string $input, int $mx, int $my) {
	$murderbots = process_input($input, $mx, $my);

	$repsx = 4;
	$repsy = 2;
	$scale = 4;

	$imgw = ($repsx * $mx + ($repsx+1) * 10) * $scale;
	$imgh = ($repsy * $my + ($repsy+1) * 10) * $scale;
	$i = imagecreatetruecolor($imgw, $imgh);
	$c0 = imagecolorallocate($i, 255, 255, 255);
	$c1 = imagecolorallocate($i, 255, 255, 180);
	$c2 = imagecolorallocate($i, 6, 160, 10);
	$c4 = imagecolorallocate($i, 180, 36, 36);

	imagefill($i, 1, 1, $c4);

	$drawn = 0;
	$elapsed = 0;

	while ($drawn < $repsx * $repsy) {
		foreach ($murderbots as $mb) {
			$mb->move();
		}
		$elapsed++;

		if (! str_contains(single_string_map($murderbots, $mx, $my), 'XXXXXXXXXX')) {
			continue;
		}

		$m = [];
		foreach ($murderbots as $b) {
			$m[$b->y][$b->x] = true;
		}

		$x0 = $drawn % $repsx;
		$y0 = (int) floor($drawn / $repsx);

		$dx0 = ($x0 * ($mx + 10) + 10) * $scale;
		$dy0 = ($y0 * ($my + 10) + 10) * $scale;

		for ($dy=0; $dy < $my; $dy++) {
			for ($dx=0; $dx < $mx; $dx++) {
				$px = $dx0 + $dx * $scale;
				$py = $dy0 + $dy * $scale;
				imagefilledrectangle($i, $px, $py, $px + ($scale-1), $py + ($scale-1), isset($m[$dy][$dx]) ? $c2 : $c1);
			}
		}

		$idrawn = $drawn + 1;
		imagestring(
			$i, 2, $dx0, $dy0 - 12, "$idrawn: $elapsed s", $c0
		);

		$drawn++;
	}

	imagegif($i, __DIR__ . "/outputs/14@{$scale}.gif");

	return true;
}

function part2 (string $input, int $mx, int $my): void {
	$murderbots = process_input($input, $mx, $my);

	$s = 0;
	while (true) {
		foreach ($murderbots as $mb) {
			$mb->move();
		}
		$s++;

		if (! has_ten_continuous($murderbots, $mx, $my)) {
			continue;
		}

		$m = [];
		foreach ($murderbots as $b) {
			$m[$b->y][$b->x] = true;
		}

		for ($dy=0; $dy < ceil($my/2); $dy++) {
			for ($dx=0; $dx < $mx; $dx++) {
				print isset($m[$dy*2][$dx])
					? (
						isset($m[$dy*2+1][$dx])
						? '█' : '▀'
					) : (
						isset($m[$dy*2+1][$dx])
						? '▄' : ' '
					);
			}
			print "\n";
		}
		println("Elapsed: {$s}s");
		break;
	}
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = part1($input_demo, 11, 7);
println('1) Result of demo: ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 12);

$p = microtime(true);
println('1) Result of real input: ' . part1($input, 101, 103));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

// 2
$p = microtime(true);
part2($input, 101, 103, 120);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
