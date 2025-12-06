package main

import (
	"fmt"
	"slices"
	"strconv"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("05")
var inputdemo string = `
3-5
10-14
16-20
12-18

1
5
8
11
17
32
`

type Range struct {
	s int
	e int
}

func parseinput(i string) ([]Range, []int) {
	tmp := utils.TrimSplit(i, "\n\n")
	rangesstr, ingredientsstr := tmp[0], tmp[1]

	ranges := []Range{}
	for _, r := range utils.TrimSplit(rangesstr, "\n") {
		rstr := utils.TrimSplit(r, "-")
		rs, err := strconv.Atoi(rstr[0])
		if err != nil {
			panic(err)
		}
		re, err := strconv.Atoi(rstr[1])
		if err != nil {
			panic(err)
		}
		ranges = append(ranges, Range{s: rs, e: re})
	}

	ingredients := []int{}
	for _, istr := range utils.TrimSplit(ingredientsstr, "\n") {
		i, err := strconv.Atoi(istr)
		if err != nil {
			panic(err)
		}
		ingredients = append(ingredients, i)
	}

	return ranges, ingredients
}

func SortAndMergeRanges(ranges *[]Range) {
	slices.SortFunc(*ranges, func(a Range, b Range) int {
		scmp := a.s - b.s
		if scmp != 0 {
			return scmp
		}
		return a.e - b.e
	})

	for i, r := range *ranges {
		if r.s == -1 {
			continue
		}
		for j := i + 1; j < len(*ranges); j++ {
			cmpr := (*ranges)[j]
			if cmpr.s == -1 {
				continue
			}
			if r.e < cmpr.s {
				break
			}
			if r.e >= cmpr.e {
				(*ranges)[j] = Range{-1, -1}
				continue
			}
			r.e = cmpr.e
			(*ranges)[i].e = cmpr.e

			(*ranges)[j] = Range{-1, -1}
		}
	}
}

func Part1(ranges *[]Range, ingredients *[]int) int {
	fc := 0
	li := 0
	for _, i := range *ingredients {
		for _, r := range *ranges {
			li++
			if i <= r.e && i >= r.s {
				fc++
				break
			}
		}
	}
	fmt.Println(li)
	return fc
}

// Wrong p2 answers:
// 315029540725894
// 314662503062125
// 334877939080182
func Part2(ranges *[]Range) int {
	c := 0
	for _, r := range *ranges {
		if r.s == -1 {
			continue
		}
		c += r.e - r.s + 1
	}
	return c
}

func main() {
	start := time.Now()

	rd, id := parseinput(inputdemo)
	ri, ii := parseinput(input)
	SortAndMergeRanges(&rd)
	SortAndMergeRanges(&ri)

	parsetime := time.Since(start)

	start1 := time.Now()

	var r1 int
	demo1expected := 3
	r1 = Part1(&rd, &id)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = Part1(&ri, &ii)
	fmt.Printf("Part 1: %d\n", r1)

	p01time := time.Since(start1)

	//
	// Part 02
	//
	start2 := time.Now()

	var r2 int
	demo2expected := 14
	r2 = Part2(&rd)
	if r2 != demo2expected {
		panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	}
	r2 = Part2(&ri)
	fmt.Printf("Part 2: %d\n", r2)

	p02time := time.Since(start2)

	//
	// Output
	//
	fmt.Println()
	fmt.Println("Runtimes ↴")
	fmt.Printf("Parse demo+input: %s\n", parsetime)
	fmt.Printf("Day 05 Part 1: %s\n", p01time)
	fmt.Printf("Day 05 Part 2: %s\n", p02time)
	fmt.Printf("Day 05 Total : %s\n", time.Since(start))
}
