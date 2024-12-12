import 'dart:io';

Iterable<String> _input() => File('inputs/2015/day_03.txt').readAsStringSync().split('').cast<String>();

void _updatePoint(Map<String, int> where, String how) {
	switch (how) {
		case '^': where.update('y', (value) => value - 1); break;
		case 'v': where.update('y', (value) => value + 1); break;
		case '<': where.update('x', (value) => value - 1); break;
		case '>': where.update('x', (value) => value + 1); break;
		default: break;
	}
}

void main() {
	// Get input
	var movements = _input();

	// Run part 1
	Map<String, int> current = {'x': 0, 'y': 0};
	List<String> houses = [];
	for (var m in movements) {
		_updatePoint(current, m);
		houses.add('${current}');
	}
	stdout.write(' A: ${houses.toSet().length}');

	// Run part 2
	houses.clear();
	Map<String, int> robo = {'x': 0, 'y': 0};
	Map<String, int> santa = {'x': 0, 'y': 0};
	int steps = 0;

	for (var m in movements) {
		var which = (steps++ % 2 == 1) ? robo : santa;
		_updatePoint(which, m);
		houses.add('${which}');
	}

	stdout.write(' B: ${houses.toSet().length}');
}