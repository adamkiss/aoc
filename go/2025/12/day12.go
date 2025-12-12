package main

import (
	"fmt"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("12")
var inputbytes []byte = utils.ReadInputBytes("12")
var inputdemo string = `
0:
###
##.
##.

1:
###
##.
.##

2:
.##
###
##.

3:
##.
###
##.

4:
###
#..
###

5:
###
.#.
###

4x4: 0 0 0 0 2 0
12x5: 1 0 1 0 2 2
12x5: 1 0 1 0 3 2
`
var inputdemobytes []byte = []byte(inputdemo)

type Shape struct {
	area int
	src  string
}

type Target struct {
	w, h, size int
	use        []int
}

func Parse(i string) (s []Shape, t []Target) {
	shapesraw := utils.TrimSplit(i, "\n\n")
	for i := 0; i < len(shapesraw)-1; i++ {
		sraw := strings.TrimLeft(shapesraw[i], strconv.Itoa(i)+":\n")
		size := strings.Count(sraw, "#")
		s = append(s, Shape{size, sraw})
	}
	for _, target := range utils.TrimSplit(shapesraw[len(shapesraw)-1], "\n") {
		whstr, shapesraw, _ := strings.Cut(target, ": ")
		wstr, hstr, _ := strings.Cut(whstr, "x")
		w, h := atoi(wstr), atoi(hstr)
		shapesstr := strings.Fields(shapesraw)
		shapes := []int{}
		for _, sstr := range shapesstr {
			shapes = append(shapes, atoi(sstr))
		}

		t = append(t, Target{w: w, h: h, size: w * h, use: shapes})
	}
	return
}

func ParseBytes(i string) (s []Shape, t []Target) {
	return
}

func atoi(s string) int {
	i, _ := strconv.Atoi(s)
	return i
}

func Part1(shapes []Shape, targets []Target) int {
	pass := 0
	for _, t := range targets {
		reqarea := 0
		for i, amt := range t.use {
			reqarea += shapes[i].area * amt
		}

		if reqarea*117 <= t.size*100 {
			pass++
		}
	}
	return pass
}

func main() {
	start := time.Now()

	shapesd, targetsd := Parse(inputdemo)
	shapesi, targetsi := Parse(input)

	parsetime := time.Since(start)

	start1 := time.Now()

	var r1 int
	demo1expected := 2
	r1 = Part1(shapesd, targetsd)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = Part1(shapesi, targetsi)
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
	fmt.Printf("Parse        : %s\n", parsetime)
	fmt.Printf("Day 12 Part 1: %s\n", p01time)
	fmt.Printf("Day 12 Part 2: %s\n", p02time)
	fmt.Printf("Day 12 Total : %s\n", time.Since(start))
}
