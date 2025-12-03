package days

import (
	"fmt"
	"strconv"
	"time"

	"github.com/adamkiss/aoc/2025/utils"
)

var daystr string = "01"
var input string = utils.ReadInput(daystr)
var inputdemo string = `
L68
L30
R48
L5
R60
L55
L1
L99
R14
L82
`

func part1(i string) int {
	pos, zeros := 50, 0

	for _, t := range utils.TrimLines(i) {
		dir := 1
		if t[0] == 'L' {
			dir = -1
		}

		amt, err := strconv.Atoi(string(t[1:]))
		if err != nil {
			panic(err)
		}
		pos += dir * amt
		for pos < 0 {
			pos += 100
		}
		for pos > 99 {
			pos -= 100
		}
		if pos == 0 {
			zeros += 1
		}
	}

	return zeros
}

func part2(i string) int {
	lpos, pos, zeros := 50, 50, 0
	hadloopedr := false

	for _, t := range utils.TrimLines(i) {
		lpos = pos
		hadloopedr = false

		dir := 1
		if t[0] == 'L' {
			dir = -1
		}

		amt, err := strconv.Atoi(string(t[1:]))
		if err != nil {
			panic(err)
		}
		pos += dir * amt
		for pos < 0 {
			pos += 100
			zeros += 1
		}
		for pos > 99 {
			hadloopedr = true
			pos -= 100
			zeros += 1
		}
		if pos == 0 && !hadloopedr {
			zeros += 1
		}
		if lpos == 0 && dir == -1 {
			zeros -= 1
		}
	}

	return zeros
}

func Day01() {
	start := time.Now()

	demo1expected := 3
	var r1 int
	r1 = part1(inputdemo)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = part1(input)
	fmt.Printf("Part 1: %d\n", r1)

	p01time := time.Since(start)

	//
	// Part 02
	//
	start = time.Now()

	var r2 int
	demo2expected := 6
	r2 = part2(inputdemo)
	if r2 != demo2expected {
		panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	}
	r2 = part2(input)
	fmt.Printf("Part 2: %d\n", r2)

	p02time := time.Since(start)

	//
	// Output
	//
	fmt.Println()
	fmt.Println("Runtimes ↴")
	fmt.Printf("Day %s Part 1: %s\n", daystr, p01time)
	fmt.Printf("Day %s Part 2: %s\n", daystr, p02time)
}
