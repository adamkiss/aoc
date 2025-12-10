package main

import (
	"fmt"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("10")
var inputdemo string = `
[.##.] (3) (1,3) (2) (2,3) (0,2) (0,1) {3,5,4,7}
[...#.] (0,2,3,4) (2,3) (0,4) (0,1,2) (1,2,3,4) {7,5,12,7,2}
[.###.#] (0,1,2,3,4) (0,3,4) (0,1,2,4,5) (1,2) {10,11,11,5,10,5}
`

type Button struct {
	raw   string
	index int
	val   int
}

type Machine struct {
	target   int
	buttons  []Button
	joltages []int
}

type PressResult struct {
	result  int
	presses []*Button
}

func findfastestsolution(m Machine) int {
	q := map[int]bool{0: true}

	iterations := 0
	for {
		iterations++

		nq := map[int]bool{}
		for val, _ := range q {
			for _, b := range m.buttons {
				pressres := val ^ b.val
				if pressres == m.target {
					return iterations
				}
				nq[pressres] = true
			}
		}
		q = nq

		if iterations == 10_000 {
			panic("Million iterations reached, probably dead end.")
		}
	}
}

func ParseMachine(s string) Machine {
	p := strings.Split(s, " ")

	target := 0
	for i, c := range strings.Trim(p[0], "[]") {
		if c == '#' {
			target += 1 << i
		}
	}

	buttons := []Button{}
	for i, btnstr := range p[1 : len(p)-1] {
		bits := strings.Split(strings.Trim(btnstr, "()"), ",")
		btnval := 0
		for _, bitstr := range bits {
			bit, _ := strconv.Atoi(bitstr)
			btnval += 1 << bit
		}
		buttons = append(buttons, Button{btnstr, i, btnval})
	}

	joltagesraw := strings.Split(strings.Trim(p[2], "{}"), ",")
	joltages := make([]int, len(joltagesraw))
	for i, jstr := range joltagesraw {
		j, _ := strconv.Atoi(jstr)
		joltages[i] = j
	}

	return Machine{
		target:   target,
		buttons:  buttons,
		joltages: joltages,
	}
}

func ParseInput(i string) []Machine {
	rawmachines := utils.TrimSplit(i, "\n")
	m := make([]Machine, len(rawmachines))
	for i, rm := range rawmachines {
		m[i] = ParseMachine(rm)
	}
	return m
}

func Part1(machines []Machine) int {
	// var observable = []Result{}

	var steps = 0
	for _, m := range machines {
		s := findfastestsolution(m)
		steps += s
	}

	return steps
}

func Part2(i string) int {
	return 2
}

func main() {
	start := time.Now()

	machinesd := ParseInput(inputdemo)
	machinesi := ParseInput(input)

	parsetime := time.Since(start)

	start1 := time.Now()

	var r1 int
	demo1expected := 7
	r1 = Part1(machinesd)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = Part1(machinesi)
	fmt.Printf("Part 1: %d\n", r1)

	p01time := time.Since(start1)

	//
	// Part 02
	//
	start2 := time.Now()

	// var r2 int
	// demo2expected := 2
	// r2 = Part2(inputdemo)
	// if r2 != demo2expected {
	// 	panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	// }
	// r2 = Part2(input)
	// fmt.Printf("Part 2: %d\n", r2)

	p02time := time.Since(start2)

	//
	// Output
	//
	fmt.Println()
	fmt.Println("Runtimes ↴")
	fmt.Printf("Day 10 -Parse: %s\n", parsetime)
	fmt.Printf("Day 10 Part 1: %s\n", p01time)
	fmt.Printf("Day 10 Part 2: %s\n", p02time)
	fmt.Printf("Day 10 Total : %s\n", time.Since(start))
}
