package main

import (
	"fmt"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("XY")
var inputdemo string = `
ABC
`

func Part1(i string) int {
	return 1
}

func Part2(i string) int {
	return 2
}

func main() {
	start := time.Now()

	var r1 int
	demo1expected := 1
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
	fmt.Printf("Day XY Part 1: %s\n", p01time)
	fmt.Printf("Day XY Part 2: %s\n", p02time)
	fmt.Printf("Day XY Total : %s\n", time.Since(start))
}
