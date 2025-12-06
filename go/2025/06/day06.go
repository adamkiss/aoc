package main

import (
	"fmt"
	"regexp"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("06")
var inputdemo string = `
123 328  51 64 
 45 64  387 23 
  6 98  215 314
*   +   *   +  
`

func parseinput1(i string) (numbers [][]int, ops []string) {
	cleaner := regexp.MustCompile(` +`)
	for _, line := range utils.TrimSplit(i, "\n") {
		line = strings.Trim(line, " ")
		line = cleaner.ReplaceAllString(line, " ")
		cols := strings.Split(line, " ")

		if strings.Contains(line, "*") {
			ops = cols
			continue
		}

		nums := make([]int, len(cols))
		for i, n := range cols {
			nums[i], _ = strconv.Atoi(n)
		}

		numbers = append(numbers, nums)
	}
	return numbers, ops
}

func parseinput2(i string) (numbers [][]int, ops []rune) {
	lines := strings.Split(strings.Trim(i, "\n"), "\n")
	opline := len(lines) - 1
	numlines := len(lines) - 2
	emptycol := strings.Repeat(" ", opline)

	numgroup := []int{}

	for i, chr := range lines[opline] {
		if chr != ' ' {
			ops = append(ops, chr)
		}
		numrunes := make([]string, opline)
		for j := 0; j <= numlines; j++ {
			numrunes[j] = string(lines[j][i])
		}
		numstr := strings.Join(numrunes, "")

		if strings.Compare(numstr, emptycol) == 0 {
			numbers = append(numbers, numgroup)
			numgroup = []int{}
			continue
		}

		num, _ := strconv.Atoi(strings.Trim(numstr, " "))
		numgroup = append(numgroup, num)
	}
	// There's unappended numgroup after loop
	numbers = append(numbers, numgroup)
	return numbers, ops
}

func Part1(i string) int {
	numbers, ops := parseinput1(i)
	result := 0
	for col, op := range ops {
		colres := 0
		if op == "*" {
			colres = 1
		}
		for row := 0; row < len(numbers); row++ {
			if op == "+" {
				colres += numbers[row][col]
			} else {
				colres *= numbers[row][col]
			}
		}
		result += colres
	}
	return result
}

func Part2(i string) int {
	numbers, ops := parseinput2(i)
	result := 0
	for grp, op := range ops {
		grpres := 0
		if op == '+' {
			for _, i := range numbers[grp] {
				grpres += i
			}
		} else {
			grpres = 1
			for _, i := range numbers[grp] {
				grpres *= i
			}
		}
		result += grpres
	}
	return result
}

func main() {
	start := time.Now()

	var r1 int
	demo1expected := 4277556
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
	demo2expected := 3263827
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
	fmt.Printf("Day 06 Part 1: %s\n", p01time)
	fmt.Printf("Day 06 Part 2: %s\n", p02time)
	fmt.Printf("Day 06 Total : %s\n", time.Since(start))
}
