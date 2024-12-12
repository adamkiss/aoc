<?php

require_once __DIR__ . '/vendor/autoload.php';

$input = trim(file_get_contents(__DIR__ . '/inputs/11.txt'));
$dinput = <<<INPUT
125 17
INPUT;

function partKeys (string $input, int $blinks = 22): int {
	if (true) {
		$d = new PDO('sqlite::memory:');
	} else {
		$h = md5($input);
		`rm -f db/$h.sqlite; touch db/$h.sqlite`;
		$d = new PDO("sqlite:db/$h.sqlite");
	}

	$initial = join(", ", array_map(fn ($i) => "($i, 1)", explode(' ', $input)));
	$d->exec(<<<SQL
	CREATE TABLE IF NOT EXISTS keys_0 (
		key INTEGER PRIMARY KEY,
		count INTEGER,
		rem INT AS (length(key)%2) STORED,
		len INT AS (length(key)/2) STORED
	)
	SQL);
	$d->exec('CREATE UNIQUE INDEX IF NOT EXISTS key_idx ON keys_0 (key)');
	$d->exec("INSERT INTO keys_0 (key, count) VALUES $initial");

	for ($i=1; $i < $blinks + 1; $i++) {
		$p = $i - 1;

		array_map(fn ($q) => $d->exec($q), explode(';', <<<SQL
		CREATE TABLE IF NOT EXISTS keys_$i (
			key INTEGER PRIMARY KEY,
			count INTEGER,
			rem INT GENERATED ALWAYS AS (length(key)%2) STORED,
			len INT GENERATED ALWAYS AS (length(key)/2) STORED
		);
		CREATE UNIQUE INDEX IF NOT EXISTS key_idx ON keys_$i (key);
		CREATE INDEX IF NOT EXISTS rem_idx ON keys_$i (rem);

		WITH NEXT_LOOP as (
			SELECT CASE
				WHEN key IS 0 THEN 1
				WHEN rem IS 0 THEN substr(key, 1, len)
				ELSE key * 2024
			END key, count FROM keys_$p
		)
		INSERT INTO keys_$i (key, count)
		SELECT key, count FROM NEXT_LOOP WHERE 1
		ON CONFLICT(key)
		DO UPDATE SET count = count + excluded.count;

		WITH THE_REST as (
			SELECT substr(key, -len) as key, count
			FROM keys_$p
			WHERE rem is 0
		)
		INSERT INTO keys_$i (key, count)
		SELECT key, count FROM THE_REST WHERE 1
		ON CONFLICT(key)
		DO UPDATE SET count = count + excluded.count
	SQL));
	}

	return $d->query("SELECT sum(count) as total FROM keys_{$blinks}")->fetchColumn(0);
}

$s = microtime(true);

// 1
$p = microtime(true);
$r = partKeys($dinput, 6);
println('1) Result of demo (6): ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 22);

$p = microtime(true);
$r = partKeys($dinput, 25);
println('1) Result of demo2 (25): ' . $r);
printf("» %.3fms\n", (microtime(true)-$p) * 1000);
assert($r === 55312);

$p = microtime(true);
println('1) Result of real input: ' . partKeys($input, 25));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

println();

// 2
$p = microtime(true);
println('2) Result of real input: ' . partKeys($input, 75));
printf("» %.3fms\n", (microtime(true)-$p) * 1000);

println();
printf("TOTAL: %.3fms\n", (microtime(true)-$s) * 1000);
