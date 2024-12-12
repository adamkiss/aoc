import 'dart:convert';
import 'dart:io';
import 'package:crypto/crypto.dart' as crypto;

String _input() => 'iwrupvqb';

void main() {
 	// Run part 1
	int number = 1;
	bool found_A = false;
	while (true) {
		String test = crypto.md5.convert(utf8.encode('${_input()}${number++}')).toString();
		if (test.startsWith('00000') && ! found_A) {
			stdout.write(' A: ${number-1}'); found_A = true;
		}
		if (test.startsWith('000000')) {
			stdout.write(' B: ${number-1}');
			return;
		}
	}
}