package main

import (
	"fmt"
	"slices"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
	"github.com/draffensperger/golp"
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
	bits  []int
	val   int
}

type Machine struct {
	target   int
	buttons  []Button
	joltages []int
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
		bitsstr := strings.Split(strings.Trim(btnstr, "()"), ",")
		btnval := 0
		bits := make([]int, len(bitsstr))
		for i, bitstr := range bitsstr {
			bit, _ := strconv.Atoi(bitstr)
			bits[i] = bit
			btnval += 1 << bit
		}
		buttons = append(buttons, Button{
			raw:   btnstr,
			val:   btnval,
			index: i,
			bits:  bits,
		})
	}

	joltagesraw := strings.Split(strings.Trim(p[len(p)-1], "{}"), ",")
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

func machineTurnOn(m Machine) int {
	q := map[int]bool{0: true}

	iterations := 0
	for {
		iterations++

		nq := map[int]bool{}
		for val := range q {
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

func machineJoltage(m Machine) int {
	nrb := len(m.buttons)
	nrj := len(m.joltages)

	lp := golp.NewLP(0, nrb)
	lp.SetVerboseLevel(golp.NEUTRAL)

	objcoeffs := make([]float64, nrb)
	for i := range nrb {
		objcoeffs[i] = 1.0
		lp.SetInt(i, true)
		lp.SetBounds(i, 0.0, float64(200))
	}
	lp.SetObjFn(objcoeffs)

	for i := range nrj {
		var entries []golp.Entry
		for j, btn := range m.buttons {
			if slices.Contains(btn.bits, i) {
				entries = append(entries, golp.Entry{Col: j, Val: 1.0})
			}
		}
		targetValue := float64(m.joltages[i])
		if err := lp.AddConstraintSparse(entries, golp.EQ, targetValue); err != nil {
			panic(err)
		}
	}

	status := lp.Solve()
	if status != golp.OPTIMAL {
		panic("lp.Solve() status isnt optimal, but " + status.String())
	}

	solution := lp.Variables()
	presses := 0
	for _, val := range solution {
		presses += int(val + .5)
	}

	return presses
}

func Part1(machines []Machine) int {
	var presses = 0
	for _, m := range machines {
		p := machineTurnOn(m)
		presses += p
	}

	return presses
}

func Part2(machines []Machine) int {
	presses := 0
	for _, m := range machines {
		p := machineJoltage(m)
		presses += p
	}

	return presses
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

	var r2 int
	demo2expected := 33
	r2 = Part2(machinesd)
	if r2 != demo2expected {
		panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	}
	r2 = Part2(machinesi)
	fmt.Printf("Part 2: %d\n", r2)

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
