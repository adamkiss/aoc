import 'dart:collection';
import 'dart:developer';
import 'dart:io';
import 'dart:math';

import 'package:tuple/tuple.dart';

Iterable<String> _input() => File('inputs/2015/day_06.txt').readAsLinesSync();

List<Tuple2<int,int>> _area_to_list(int startX, int startY, int endX, int endY) {
	List<Tuple2<int,int>> result = [];
	for (var j = startY; j <= endY; j++) {
		result.add(Tuple2(j * 1000 + startX, j * 1000 + endX + 1)); // +1 changes range inclusive -> exclusive
	}
	return result;
}

void main() {
	final parser = RegExp(r'^(?<action>.*) (?<sX>\d+),(?<sY>\d+) through (?<eX>\d+),(?<eY>\d+)');
	final parsed_instructions = _input().map((e) {
		final m = parser.firstMatch(e);
		return Tuple2<String,List>(
			m!.namedGroup('action')!,
			_area_to_list(
				int.parse(m.namedGroup('sX')!, radix: 10), int.parse(m.namedGroup('sY')!, radix: 10),
				int.parse(m.namedGroup('eX')!, radix: 10), int.parse(m.namedGroup('eY')!, radix: 10)
			)
		);
	});

	/* PART A: RUN 1 */
	var lightsA = List<bool>.filled(1000000, false);
	for (final Tuple2<String, List> instr in parsed_instructions) {
		if (instr.item1.startsWith('turn')) {
			for (final range in instr.item2) {
				lightsA.fillRange(range.item1, range.item2, instr.item1 == 'turn on');
			}
			continue;
		}

		for (final range in instr.item2) {
			lightsA.setRange(
				range.item1, range.item2,
				lightsA.sublist(range.item1, range.item2).map((e) => !e)
			);
		}
	}
	stdout.write(' A: ${lightsA.where((l) => l == true).length}');

	/* PART B: RUN 1 */
	var lightsB = List<int>.filled(1000000, 0);
	final Map<String, int> changeB = {
		'turn on' : 1,
		'turn off': -1,
		'toggle'  : 2
	};
	for (final Tuple2<String, List> instr in parsed_instructions) {
		for (final range in instr.item2) {
			lightsB.setRange(
				range.item1, range.item2,
				lightsB.sublist(range.item1, range.item2).map((e) => max(0, e + (changeB[instr.item1] ?? 0)))
			);
		}
	}
	stdout.write(' B: ${lightsB.reduce((total, curr) => curr + total)}');
}