package days

import (
	"fmt"
	"time"

	"github.com/adamkiss/aoc/2025/utils"
)

var daystr string = "XY"
var input string = utils.ReadInput(daystr)
var inputdemo string = `
ABC
`

func part1(i string) int {
	return 1
}

func part2(i string) int {
	return 2
}

func DayXY() {
	start := time.Now()

	var r1 int
	demo1expected := 1
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

	// var r2 int
	// demo2expected := 2
	// r2 = part2(inputdemo)
	// if r2 != demo2expected {
	// 	panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	// }
	// r2 = part2(input)
	// fmt.Printf("Part 2: %d\n", r2)

	p02time := time.Since(start)

	//
	// Output
	//
	fmt.Println()
	fmt.Println("Runtimes ↴")
	fmt.Printf("Day %s Part 1: %s\n", daystr, p01time)
	fmt.Printf("Day %s Part 2: %s\n", daystr, p02time)
}
