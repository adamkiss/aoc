package main

import (
	"fmt"
	"strconv"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("01")
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

func Part1(i string) int {
	pos, zeros := 50, 0

	for _, t := range utils.TrimSplit(i, "\n") {
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

func Part2(i string) int {
	lpos, pos, zeros := 50, 50, 0
	hadloopedr := false

	for _, t := range utils.TrimSplit(i, "\n") {
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

func main() {
	start := time.Now()

	demo1expected := 3
	var r1 int
	r1 = Part1(inputdemo)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = Part1(input)
	fmt.Printf("Part 1: %d\n", r1)

	p01time := time.Since(start)

	//
	// Part 02
	//
	start2 := time.Now()

	var r2 int
	demo2expected := 6
	r2 = Part2(inputdemo)
	if r2 != demo2expected {
		panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	}
	r2 = Part2(input)
	fmt.Printf("Part 2: %d\n", r2)

	p02time := time.Since(start2)

	//
	// Output
	//
	fmt.Println()
	fmt.Println("Runtimes ↴")
	fmt.Printf("Day 01 Part 1: %s\n", p01time)
	fmt.Printf("Day 01 Part 2: %s\n", p02time)
	fmt.Printf("Day 01 Total : %s\n", time.Since(start))
}
