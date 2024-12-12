import 'dart:io';
import 'extensions.dart';

List<String> _input() => File('inputs/2015/day_02.txt').readAsLinesSync();

void main() {
	// Get input
	Iterable<List<int>> dimensions = _input()
		.map((e) => e.split('x').map((e) => int.parse(e, radix: 10)))
		.map((Iterable<int> e) => e.toList().cast<int>()..sort());

	// Run part 1
	num total = 0;
	for (var box in dimensions) {
		total += (
			2 * box[0] * box[1] +
			2 * box[1] * box[2] +
			2 * box[0] * box[2] +
			box[0] * box[1]
		);
	}
	stdout.write(' A: ${total}');

	// Run part 2
	num length = 0;
	for (var box in dimensions) {
		length += (
			2 * box[0] +
			2 * box[1] +
			box.product()
		);
	}
	stdout.write(' B: ${length}');
}