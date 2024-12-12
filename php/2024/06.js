#!/usr/bin/env node

import { count } from 'console';
import {readFileSync} from 'fs'
import {resolve} from 'path'

const die = (message) => { console.log(message); process.exit(1) }
const toMap = input => input.split("\n").map(line => line.split(''))
const printMap = map => console.log(map.map(line => line.join('')).join("\n")+ "\n")

const vectorMap = new Map([
	['^', [0, -1]],
	['v', [0, 1]],
	['<', [-1, 0]],
	['>', [1, 0]],
]);
const turnMap = new Map([
	[[0, -1].join(), [1, 0]],
	[[0, 1].join(), [-1, 0]],
	[[-1, 0].join(), [0, -1]],
	[[1, 0].join(), [0, 1]],
]);


function isMapTraversalLooped(map, pos, vec) {
	let justTurned = false;
	let turns = [];
	do {
		let newPos = [pos[0] + vec[0], pos[1] + vec[1]];
		// Out of bounds
		if (
			newPos[0] < 0 ||
			newPos[0] >= map[0].length ||
			newPos[1] < 0 ||
			newPos[1] >= map.length
		) {
			return false;
		}

		if (
			map[newPos[1]][newPos[0]] === '#' ||
			map[newPos[1]][newPos[0]] === 'O'
		) {
			vec = turnMap.get(vec.join());
			let off = `${pos.join()}-${vec.join()}`;
			if (turns.includes(off)) {
				return true;
			}

			turns.push(off);
			map[pos[1]][pos[0]] = '+';
		} else {
			pos = newPos;
			let char;
			switch (vec.toString()) {
				case '0,-1':
				case '0,1':
					char = '|';
					break;
				case '-1,0':
				case '1,0':
					char = '-';
					break;
			}
			if (map[pos[1]][pos[0]] === '.') {
				map[pos[1]][pos[0]] = char;
			}
		}
	} while (true);
}

const demo = `....#.....
.........#
..........
..#.......
.......#..
..........
.#..^.....
........#.
#.........
......#...`
const input = readFileSync(resolve('../../inputs/2024/06.txt'), 'utf-8').trim()

function part1(input) {
	let map = toMap(input);
	let pos = null;
	let vec = null;

	map.forEach((line, y) => {
		if (pos !== null) {
			return;
		}

		line.forEach((symbol, x) => {
			if (vectorMap.has(symbol)) {
				pos = [x, y];
				vec = vectorMap.get(symbol);
			}
		});
	});

	let newPos
	do {
		newPos = [pos[0] + vec[0], pos[1] + vec[1]];
		if (
			newPos[0] < 0
			|| newPos[0] >= map[0].length
			|| newPos[1] < 0
			|| newPos[1] >= map.length
		) {
			break;
		} else if (map[newPos[1]][newPos[0]] === '#') {
			vec = turnMap.get(vec.join());
		} else {
			pos = newPos;
			map[newPos[1]][newPos[0]] = 'X';
		}
	} while (true);

	let count = map.reduce((total, line) => {
		return total + line.reduce((subtotal, symbol) => {
			return subtotal + (symbol === 'X' ? 1 : 0);
		}, 0);
	}, 0);

	return {count, map};
}

function part2(input) {
	let map = toMap(input);
	let pos = null;
	let vec = null;

	map.forEach((line, y) => {
		if (pos !== null) {
			return;
		}

		line.forEach((symbol, x) => {
			if (vectorMap.has(symbol)) {
				pos = [x, y];
				vec = vectorMap.get(symbol);
			}
		});
	});

	let solved = part1(input).map;
	let looped = 0;

	solved.forEach((line, y) => {
		line.forEach((symbol, x) => {
			if (symbol === 'X') {
				let testMap = map.slice().map(arr => arr.slice());
				testMap[y][x] = 'O';

				if (isMapTraversalLooped(testMap, pos, vec)) {
					looped++;
				}
			}
		});
	});

	return looped;
}

// PART 1
console.info('1) Result of demo: ', part1(demo).count);
console.info('1) Result of real input: ', part1(input).count);
console.log('–––');
console.info('2) Result of demo: ', part2(demo));
console.info('2) Result of real input: ', part2(input));
