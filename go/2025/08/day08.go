package main

import (
	"fmt"
	"math"
	"slices"
	"sort"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("08")
var inputdemo string = `
162,817,812
57,618,57
906,360,560
592,479,940
352,342,300
466,668,158
542,29,236
431,825,988
739,650,466
52,470,668
216,146,977
819,987,18
117,168,530
805,96,715
346,949,466
970,615,88
941,993,340
862,61,35
984,92,344
425,690,689
`

type Vec3 struct {
	x int
	y int
	z int
}

type Edge struct {
	From, To int
	distsqr  uint64
}

func dsu_find(parent []int, i int) int {
	if parent[i] == i {
		return i
	}
	return dsu_find(parent, parent[i])
}

func dsu_union(parent, size []int, x, y int) bool {
	xp, yp := dsu_find(parent, x), dsu_find(parent, y)
	if xp == yp {
		return false
	}
	if size[xp] < size[yp] {
		xp, yp = yp, xp
	}
	parent[yp] = xp
	size[xp] += size[yp]
	return true
}

type Circuit []int

func (c Circuit) Overlaps(check Circuit) bool {
	for _, jb := range check {
		if slices.Contains(c, jb) {
			return true
		}
	}
	return false
}

func StringToJunctionBox(s string) Vec3 {
	parts := strings.Split(s, ",")
	x, _ := strconv.Atoi(parts[0])
	y, _ := strconv.Atoi(parts[1])
	z, _ := strconv.Atoi(parts[2])
	return Vec3{x, y, z}
}

func (jf Vec3) DistanceTo(jt Vec3) float64 {
	return math.Sqrt(float64(jf.DistanceToSquare(jt)))
}

func (jf Vec3) DistanceToSquare(jt Vec3) int {
	dx := jf.x - jt.x
	dy := jf.y - jt.y
	dz := jf.z - jt.z
	return dx*dx + dy*dy + dz*dz

}

func Parse(i string) ([]Vec3, []Edge) {
	lines := utils.TrimSplit(i, "\n")
	boxes := make([]Vec3, len(lines))
	for i, jbstr := range lines {
		boxes[i] = StringToJunctionBox(jbstr)
	}

	// get the edges
	edges := []Edge{}
	for from, boxfrom := range boxes {
		for to := from + 1; to < len(boxes); to++ {
			edges = append(edges, Edge{from, to, uint64(boxfrom.DistanceToSquare(boxes[to]))})
		}
	}
	sort.Slice(edges, func(l, r int) bool { return edges[l].distsqr < edges[r].distsqr })

	return boxes, edges
}

func Part1(boxes []Vec3, edges []Edge, conncount int) int {
	boxcount := len(boxes)

	parent := make([]int, boxcount)
	size := make([]int, boxcount)
	for i := range parent {
		parent[i] = i
		size[i] = 1
	}

	for i := 0; i < conncount && i < len(edges); i++ {
		dsu_union(parent, size, edges[i].From, edges[i].To)
	}

	var sizes []int
	for i := range conncount {
		if dsu_find(parent, i) == i {
			sizes = append(sizes, size[i])
		}
	}
	sort.Slice(sizes, func(l, r int) bool { return sizes[l] > sizes[r] })

	return sizes[0] * sizes[1] * sizes[2]
}

func Part2(boxes []Vec3, edges []Edge) int {
	parent := make([]int, len(boxes))
	size := make([]int, len(boxes))
	for i := range parent {
		parent[i] = i
		size[i] = 1
	}

	toconnect := len(boxes)
	var last Edge

	for _, e := range edges {
		if dsu_union(parent, size, e.From, e.To) {
			last = e
			toconnect--
			if toconnect == 1 {
				break
			}
		}
	}

	return boxes[last.From].x * boxes[last.To].x
}

func main() {
	start := time.Now()

	boxesd, edgesd := Parse(inputdemo)
	boxesi, edgesi := Parse(input)

	parsetime := time.Since(start)

	start1 := time.Now()

	var r1 int
	demo1expected := 40
	r1 = Part1(boxesd, edgesd, 10)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = Part1(boxesi, edgesi, 1000)
	fmt.Printf("Part 1: %d\n", r1)

	p01time := time.Since(start1)

	//
	// Part 02
	//
	start2 := time.Now()

	var r2 int
	demo2expected := 25272
	r2 = Part2(boxesd, edgesd)
	if r2 != demo2expected {
		panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	}
	r2 = Part2(boxesi, edgesi)
	fmt.Printf("Part 2: %d\n", r2)

	p02time := time.Since(start2)

	//
	// Output
	//
	fmt.Println()
	fmt.Println("Runtimes ↴")
	fmt.Printf("Day 08 parse: %s\n", parsetime)
	fmt.Printf("Day 08 Part 1: %s\n", p01time)
	fmt.Printf("Day 08 Part 2: %s\n", p02time)
	fmt.Printf("Day 08 Total : %s\n", time.Since(start))
}
