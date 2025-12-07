package main

import (
	"fmt"
	"log"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("07")
var inputdemo string = `
.......S.......
...............
.......^.......
...............
......^.^......
...............
.....^.^.^.....
...............
....^.^...^....
...............
...^.^...^.^...
...............
..^...^.....^..
...............
.^.^.^.^.^...^.
...............
`

type Pos struct {
	row int
	col int
}

type GridString struct {
	s string
	w int
	h int
}

func CreateGridString(s string) GridString {
	s = strings.Trim(s, "\n")
	w := strings.Index(s, "\n")
	if w == -1 {
		log.Fatal("CreateGridString: string doesn't contain any '\n' characters")
	}
	h := (len(s) + 1) / (w + 1)
	return GridString{s, w, h}
}

func (g GridString) GetRC(row int, col int) string {
	pos := g.RCToPos(row, col)
	if pos > len(g.s) {
		panic(fmt.Sprintf("GetRC: %d,%d out of bounds! \n %s", row, col, g.s))
	}
	return g.s[pos : pos+1]
}

func (g GridString) PosToRC(pos int) (row int, col int) {
	row = pos / (g.w + 1)
	col = pos % (g.w + 1)
	return row, col
}

func (g GridString) RCToPos(row int, col int) int {
	p := row*(g.w+1) + col
	return p
}

func (g GridString) OutOfBounds(row int, col int) bool {
	p := g.RCToPos(row, col)
	return row < 0 || col < 0 || row >= g.w || col >= g.h || p > len(g.s)
}

func Part1(i string) int {
	grid := CreateGridString(i)
	start := strings.Index(grid.s, "S")
	sr, sc := grid.PosToRC(start)
	beams := map[Pos]bool{{sr + 1, sc}: true}
	splits := 0

	for {
		newbeams := map[Pos]bool{}
		for beam := range beams {
			n := Pos{beam.row + 1, beam.col}
			if grid.OutOfBounds(n.row, n.col) {
				continue
			}
			char := grid.GetRC(n.row, n.col)
			if char == "." {
				newbeams[n] = true
				continue
			}
			if char == "^" {
				splits++
				nl := Pos{n.row, n.col - 1}
				n.col++
				if !grid.OutOfBounds(nl.row, nl.col) {
					newbeams[nl] = true
				}
				if !grid.OutOfBounds(n.row, n.col) {
					newbeams[n] = true
				}
			}
		}

		if len(newbeams) == 0 {
			break
		}

		beams = newbeams
	}

	return splits
}

func Part2(i string) int {
	grid := CreateGridString(i)
	start := strings.Index(grid.s, "S")
	sr, sc := grid.PosToRC(start)
	beams := map[Pos]int{{sr, sc}: 1}
	timelines := 0

	for {
		newbeams := map[Pos]int{}
		for beam, paths := range beams {
			if beam.row == grid.h-2 {
				timelines += paths
				continue
			}

			n := Pos{beam.row + 1, beam.col}
			if grid.OutOfBounds(n.row, n.col) {
				continue
			}
			char := grid.GetRC(n.row, n.col)
			if char == "." {
				newbeams[n] += paths
				continue
			}
			if char == "^" {
				nl := Pos{n.row, n.col - 1}
				n.col++
				if !grid.OutOfBounds(nl.row, nl.col) {
					newbeams[nl] += paths
				}
				if !grid.OutOfBounds(n.row, n.col) {
					newbeams[n] += paths
				}
			}
		}

		if len(newbeams) == 0 {
			break
		}

		beams = newbeams
	}

	return timelines
}

func main() {
	start := time.Now()

	var r1 int
	demo1expected := 21
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
	demo2expected := 40
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
	fmt.Printf("Day 07 Part 1: %s\n", p01time)
	fmt.Printf("Day 07 Part 2: %s\n", p02time)
	fmt.Printf("Day 07 Total : %s\n", time.Since(start))
}
