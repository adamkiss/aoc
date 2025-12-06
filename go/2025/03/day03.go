package main

import (
	"fmt"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("03")
var inputdemo string = `
987654321111111
811111111111119
234234234234278
818181911112111
`

type BankWindow []int

func largestindex(s []int) int {
	largest := 0
	largesti := -1
	for i, v := range s {
		if v <= largest {
			continue
		}
		largest = v
		largesti = i
	}
	return largesti
}

func bankjoltage(bank []int, digits int) int {
	wsize := len(bank) - digits + 1
	joltage := 0
	last := 0
	for i := 0; i < digits; i++ {
		search := bank[last : last+wsize]
		next := largestindex(search)
		if next != 0 {
			wsize -= next
		}
		last += next
		joltage = joltage*10 + bank[last]
		last += 1
	}
	return joltage
}

func Part1(i string) int {
	banks := utils.TrimSplit(i, "\n")
	joltage := 0
	for _, bank := range banks {
		bankints := make([]int, len(bank))
		for i, c := range bank {
			bankints[i] = int(c - '0')
		}
		joltage += bankjoltage(bankints, 2)
	}
	return joltage
}

func Part2(i string) int {
	banks := utils.TrimSplit(i, "\n")
	joltage := 0
	for _, bank := range banks {
		bankints := make([]int, len(bank))
		for i, c := range bank {
			bankints[i] = int(c - '0')
		}
		joltage += bankjoltage(bankints, 12)
	}
	return joltage
}

func main() {
	start := time.Now()

	var r1 int
	demo1expected := 357
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
	demo2expected := 3121910778619
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
	fmt.Printf("Day 03 Part 1: %s\n", p01time)
	fmt.Printf("Day 03 Part 2: %s\n", p02time)
	fmt.Printf("Day 03 Total : %s\n", time.Since(start))
}
