package main

import (
	"fmt"
	"sort"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("09")
var inputdemo string = `
7,1
11,1
11,7
9,7
9,5
2,5
2,3
7,3
`

type Vec2 struct {
	row, col int
}

func StringToVec2(s string) Vec2 {
	parts := strings.Split(s, ",")
	x, _ := strconv.Atoi(parts[0])
	y, _ := strconv.Atoi(parts[1])
	return Vec2{x, y}
}

func (from Vec2) AreaTo(to Vec2) int {
	c := from.col - to.col
	if c < 0 {
		c = -c
	}
	r := from.row - to.row
	if r < 0 {
		r = -r
	}
	return (r + 1) * (c + 1)
}

type Edge struct {
	from, to   Vec2
	t, l, b, r int
}

type Area struct {
	from, to Vec2
	size     int
	lt, rb   Vec2
}

func CreateArea(f, t Vec2) Area {
	s := f.AreaTo(t)
	lt := Vec2{min(f.row, t.row), min(f.col, t.col)}
	rb := Vec2{max(f.row, t.row), max(f.col, t.col)}
	return Area{from: f, to: t, size: s, lt: lt, rb: rb}
}

func CreateEdge(from, to Vec2) Edge {
	t := min(from.row, to.row)
	b := max(from.row, to.row)
	l := min(from.col, to.col)
	r := max(from.col, to.col)
	return Edge{from, to, t, l, b, r}
}

func (e Edge) DoesntCross(a Area) bool {
	if e.b <= a.lt.row || e.t >= a.rb.row {
		// outer edge or outside
		return true
	}
	if e.r <= a.lt.col || e.l >= a.rb.col {
		// outer edge or ouside
		return true
	}
	// fully inside
	return (e.l > a.lt.col && e.r < a.rb.col && e.t > a.lt.row && e.b < a.rb.row)
}

func min(a, b int) int {
	if a < b {
		return a
	}
	return b
}

func max(a, b int) int {
	if a > b {
		return a
	}
	return b
}

func Part1(i string) int {
	points := []Vec2{}
	for _, v := range utils.TrimSplit(i, "\n") {
		points = append(points, StringToVec2(v))
	}

	maxarea := -1
	for i, v := range points {
		for j := i + 1; j < len(points); j++ {
			a := v.AreaTo(points[j])
			if a > maxarea {
				maxarea = a
			}
		}
	}
	return maxarea
}

func Part2(i string) int {
	points := []Vec2{}
	for _, v := range utils.TrimSplit(i, "\n") {
		points = append(points, StringToVec2(v))
	}

	areas := []Area{}
	edges := []Edge{}
	for i, v := range points {
		for j := i + 1; j < len(points); j++ {
			areas = append(areas, CreateArea(v, points[j]))
		}
		if i != len(points)-1 {
			edges = append(edges, CreateEdge(v, points[i+1]))
		} else {
			edges = append(edges, CreateEdge(v, points[0]))
		}
	}
	sort.Slice(areas, func(l, r int) bool { return areas[l].size > areas[r].size })

	maxarea := -1
	for _, area := range areas {
		for _, edge := range edges {
			if !edge.DoesntCross(area) {
				goto nextarea
			}
		}
		maxarea = area.size
		goto done
	nextarea:
	}

done:
	return maxarea
}

func main() {
	start := time.Now()

	var r1 int
	demo1expected := 50
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
	demo2expected := 24
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
	fmt.Printf("Day 09 Part 1: %s\n", p01time)
	fmt.Printf("Day 09 Part 2: %s\n", p02time)
	fmt.Printf("Day 09 Total : %s\n", time.Since(start))
}
