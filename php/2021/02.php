<?php

use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Obj;

require_once __DIR__ . '/vendor/autoload.php';

$input = read_input();
$input_demo = <<<INPUT
forward 5
down 5
forward 8
up 3
down 8
forward 2
INPUT;

function process_input(string $input) : Collection {
	$__ = explode("\n", $input);
	$__ = array_map(function($act) {
		[$direction, $value] = explode(' ', $act);
		$value = intval($value);
		return new Obj(compact('direction', 'value'));
	}, $__);
	return new Collection($__);
}

function part1 (string $input) {
	$input = process_input($input);
	$groupped = $input->groupBy('direction');

	$distance = array_sum($groupped->get('forward')->pluck('value'));
	$depth = array_sum($groupped->get('down')->pluck('value')) - array_sum($groupped->get('up')->pluck('value'));

	return $distance * $depth;
}

function part2 (string $input) {
	$input = process_input($input);
	$aim = 0;
	$calculated = $input->map(function(Obj $action) use (&$aim) {
		if ($action->direction() === 'forward') {
			$action->depthChange = $action->value() * $aim;
			return $action;
		}
		$aim += $action->direction() === 'down' ? $action->value() : -1 * $action->value();
		return $action;
	});

	$fwdActions = $calculated->filterBy('direction', 'forward');
	return array_sum($fwdActions->pluck('value')) * array_sum($fwdActions->pluck('depthChange'));
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
