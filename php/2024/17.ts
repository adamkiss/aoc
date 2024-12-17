const ins = [2, 4, 1, 7, 7, 5, 1, 7, 4, 6, 0, 3, 5, 5, 3, 0];
// const ins = [0, 3, 5, 4, 3, 0];

const insLength = ins.length;

function doit(initialA: number = -1, compare: boolean = false) {
	const output = [];
	const ops = [
		// ADV
		(operand: number) => {
			const combo = combos[operand]();
			//console.log('A modified: ', a, '/', '(', Math.pow(2, combo), ')');
			a = Math.trunc(a / Math.pow(2, combo));
		},
		// BXL
		(operand: number) => {
			b = (b ^ operand) >>> 0;
		},
		// BST
		(operand: number) => {
			b = combos[operand]() % 8;
		},
		// JNZ
		() => {},
		// BXC
		() => {
			b = (b ^ c) >>> 0;
		},
		// OUT
		(operand: number) => {
			output.push(combos[operand]() % 8);
		},
		// BDV
		(operand: number) => {
			const combo = combos[operand]();
			b = Math.trunc(a / Math.pow(2, combo));
		},
		// CDV
		(operand: number) => {
			const combo = combos[operand]();
			c = Math.trunc(a / Math.pow(2, combo));
		},
	];

	// initialize
	let a = 0;
	if (initialA >= 0) a = initialA;
	else a = 66245665;
	let b = 0;
	let c = 0;
	let pointer = 0;

	const combos = [
		() => 0,
		() => 1,
		() => 2,
		() => 3,
		() => a,
		() => b,
		() => c,
	];

	do {
		let op = ins[pointer];
		let operand = ins[pointer + 1];
		if (op === 3 && a !== 0) {
			if (operand === pointer) {
				pointer += 2;
			} else {
				pointer = operand;
			}
		} else {
			ops[op](operand);
			pointer += 2;
		}
		if (compare && op === 5) {
			if (output.length > ins.length) break;
			for (let i = 0; i < output.length; i++) {
				if (output[i] !== ins[i]) {
					return [0];
				}
			}
		}
	} while (pointer <= ins.length - 2);

	return output;
}

function recurse(target: string, nr: string, lev: number): number|false {
	const r = doit(parseInt(nr, 8));
	if (r.length !== nr.length) {
		return false;
	}

	const rs = r.join('');
	if (rs === target) {
		console.log('found match', parseInt(nr, 8))
		return parseInt(nr, 8)
	}
	if (!target.endsWith(rs)) {
		return false;
	}

	[...Array(8)].forEach((v, i) => {
		const result = recurse(target, nr+i, lev+1)
		if (result !== false) {
			return result;
		}
	});

	return false;
}

//console.log('Pt 1:', doit(-1).join(','));

const target = ins.join('');
const start = [...Array(8)].map((v, i) => i.toString());

let winner = -1;
const nr = start
	.map(v => recurse(target, v, 1))
	.filter(v => v)
console.log(nr[0])


// let found = false;
// for (let i = 0; i < 500; i += 1) {
// 	const res = doit(i, false);
// 	console.log('a decimal', i, 'a octal:', i.toString(8), 'result:', res.join(''), 'target', target);
// 	if (res.length !== insLength) continue;
// 	if (res.join(',') === target) {
// 		console.log('FOUND! Pt 2:', i, res.join(','));
// 		found = true;
// 		break;
// 	}
// }

// min 35_1843_7208_8832
// max 281_4749_7671_0655
//
// target: 2417_7517_4603_5530
//
// min 255086697638832
//
// 269134712638832 max?

// 246290604088832
