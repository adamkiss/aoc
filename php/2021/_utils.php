<?php

function read_input(int|string $day_file = null) : string {
	$f = match(true) {
		is_int($day_file) => sprintf('%02d.txt', $day_file),
		$day_file === null => basename(debug_backtrace(limit: 1)[0]['file'], 'php') . 'txt',
		default => $day_file
	};
	return trim(file_get_contents(dirname(__DIR__, 2) . "/inputs/2021/{$f}"));
}

function println(...$strings): void {
	print join(', ', $strings);
	print "\n";
}

/**
 * Stubbing Ray
 *
 * instead of empty stub, include noop class so it doesn't die on me
 * if I forget to remove all mentions when going to prod
 */
class FakeRay {
	public function __call($name, $args): FakeRay
	{
		return $this;
	}
}
if (! function_exists('ray')) {
	function ray(...$arg) {
		return new FakeRay($arg);
	}
}
if (! function_exists('rd')) {
	function rd(...$arg) {
		new FakeRay($arg);
		die();
	}
}
