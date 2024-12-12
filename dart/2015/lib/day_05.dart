import 'dart:developer';
import 'dart:io';

Iterable<String> _input() => File('inputs/2015/day_05.txt').readAsLinesSync();

bool _is_a_nice_string_A(String str) {
	final has_vowels = RegExp(r'(?:.*?[aeiou]){3,}');
	final has_doubled_letter = RegExp(r'([a-z])\1');
	final has_disallowed_strings = RegExp(r'ab|cd|pq|xy');

	if (! has_vowels.hasMatch(str)) {
		return false;
	}

	if (! has_doubled_letter.hasMatch(str)) {
		return false;
	}

	return ! has_disallowed_strings.hasMatch(str);
}

bool _is_a_nice_string_B(String str) {
	return RegExp(r'([a-z]{2}).*?\1').hasMatch(str) && RegExp(r'([a-z]).\1').hasMatch(str);
}

void main() {
	// Run part 1
	Iterable<String> nice_strings_A = _input().where((element) => _is_a_nice_string_A(element));
	stdout.write(' A: ${nice_strings_A.length}');

	// Run part 2
	Iterable<String> nice_strings_B = _input().where((element) => _is_a_nice_string_B(element));
	inspect(nice_strings_B);
	stdout.write(' B: ${nice_strings_B.length}');
}