package main

import (
	"fmt"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("04")
var inputdemo string = `
..@@.@@@@.
@@@.@.@.@@
@@@@@.@.@@
@.@@@@..@.
@@.@@@@.@@
.@@@@@@@.@
.@.@.@.@@@
@.@@@.@@@@
.@@@@@@@@.
@.@.@@@.@.
`

func parseinput(i string) [][]rune {
	var grid [][]rune
	lines := utils.SplitTrim(i, "\n")
	for _, l := range lines {
		grid = append(grid, []rune(l))
	}
	return grid
}

func countrolls(grid *[][]rune, x int, y int) int {
	maxy := len(*grid) - 1
	maxx := len((*grid)[0]) - 1

	rolls := 0
	for i := -1; i <= 1; i++ {
		for j := -1; j <= 1; j++ {
			if x+i < 0 || x+i > maxx || y+j < 0 || y+j > maxy || (i == 0 && j == 0) {
				continue
			}
			if (*grid)[y+j][x+i] == '@' {
				rolls += 1
			}
		}
	}

	return rolls
}

func countaccessible(grid *[][]rune) int {
	accessible := 0
	for y, l := range *grid {
		for x, r := range l {
			if r == '.' {
				continue
			}

			if countrolls(grid, x, y) < 4 {
				accessible += 1
			}
		}
	}

	return accessible
}

func Part1(grid *[][]rune) int {
	return countaccessible(grid)
}

func Part2(grid [][]rune) int {
	type xy struct {
		x int
		y int
	}

	removed := 0

	for {
		c := countaccessible(&grid)
		if c == 0 {
			break
		}

		remove := []xy{}
		for y, l := range grid {
			for x, r := range l {
				if r == '.' {
					continue
				}

				if countrolls(&grid, x, y) < 4 {
					remove = append(remove, xy{x: x, y: y})
					removed += 1
				}
			}
		}
		for _, r := range remove {
			grid[r.y][r.x] = '.'
		}
	}

	return removed
}

func main() {
	start := time.Now()

	gridd := parseinput(inputdemo)
	gridi := parseinput(input)

	parsetime := time.Since(start)

	start1 := time.Now()

	var r1 int
	demo1expected := 13
	r1 = Part1(&gridd)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = Part1(&gridi)
	fmt.Printf("Part 1: %d\n", r1)

	p01time := time.Since(start1)

	//
	// Part 02
	//
	start2 := time.Now()

	var r2 int
	demo2expected := 43
	r2 = Part2(gridd)
	if r2 != demo2expected {
		panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	}
	r2 = Part2(gridi)
	fmt.Printf("Part 2: %d\n", r2)

	p02time := time.Since(start2)

	//
	// Output
	//
	fmt.Println()
	fmt.Println("Runtimes ↴")
	fmt.Printf("Parse demo+input: %s\n", parsetime)
	fmt.Printf("Day 04 Part 1: %s\n", p01time)
	fmt.Printf("Day 04 Part 2: %s\n", p02time)
	fmt.Printf("Day 04 Total : %s\n", time.Since(start))
}
