package main

import (
	"fmt"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("02")
var inputdemo string = `
11-22,95-115,998-1012,1188511880-1188511890,222220-222224,
1698522-1698528,446443-446449,38593856-38593862,565653-565659,
824824821-824824827,2121212118-2121212124
`

func digits(i int) int {
	if i == 0 {
		return 0
	}
	d := 0
	for i != 0 {
		i /= 10
		d++
	}
	return d
}

func Part1(i string) int {
	ranges := utils.TrimSplit(i, ",")
	var invalid []int
	for _, r := range ranges {
		bounds := utils.TrimSplit(r, "-")
		if len(bounds) != 2 {
			panic("invalid range")
		}
		bs, err := strconv.Atoi(bounds[0])
		if err != nil {
			panic(err)
		}
		be, err := strconv.Atoi(bounds[1])
		if err != nil {
			panic(err)
		}
		for i := bs; i <= be; i++ {
			s := strconv.Itoa(i)
			l := len(s)
			if l%2 != 0 {
				continue
			}

			if s[0:l/2] == s[l/2:] {
				invalid = append(invalid, i)
			}
		}
	}

	sum := 0
	for _, i := range invalid {
		sum += i
	}
	return sum
}

func Part2(i string) int {
	ranges := utils.TrimSplit(i, ",")
	tests := [...]int{2, 3, 5, 7}
	var invalid []int
	for _, r := range ranges {
		bounds := utils.TrimSplit(r, "-")
		if len(bounds) != 2 {
			panic("invalid range")
		}
		bs, err := strconv.Atoi(bounds[0])
		if err != nil {
			panic(err)
		}
		be, err := strconv.Atoi(bounds[1])
		if err != nil {
			panic(err)
		}

		for i := bs; i <= be; i++ {
			s := strconv.Itoa(i)
			l := len(s)
			for _, t := range tests {
				if t > l || l%t != 0 {
					continue
				}
				sub := s[0 : l/t]
				if strings.Count(s, sub) == t {
					invalid = append(invalid, i)
				}
			}
		}
	}

	unique := map[int]bool{}
	sum := 0
	for _, i := range invalid {
		if unique[i] {
			continue
		}
		unique[i] = true
		sum += i
	}
	return sum
}

func main() {
	start := time.Now()

	var r1 int
	demo1expected := 1227775554
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
	demo2expected := 4174379265
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
	fmt.Printf("Day 02 Part 1: %s\n", p01time)
	fmt.Printf("Day 02 Part 2: %s\n", p02time)
	fmt.Printf("Day 02 Total : %s\n", time.Since(start))
}
