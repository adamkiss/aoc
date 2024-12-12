import 'dart:io';

String _input() => File('inputs/2015/day_01.txt').readAsStringSync();

void main() {
	Iterable<int> steps = _input().split('').map((step) => step == '(' ? 1 : -1);

	int floor = 0;
	for (var step in steps) { floor += step; }
	stdout.write(' A: ${floor}');

	int steps_taken = 0;
	floor = 0;
	while (floor > -1) {
		floor += steps.elementAt(steps_taken++);
	}
	stdout.write(' B: ${steps_taken}');
}