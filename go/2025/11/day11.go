package main

import (
	"fmt"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("11")
var inputdemo string = `
aaa: you hhh
you: bbb ccc
bbb: ddd eee
ccc: ddd eee fff
ddd: ggg
eee: out
fff: out
ggg: out
hhh: ccc fff iii
iii: out
`

var inputdemo2 string = `
svr: aaa bbb
aaa: fft
fft: ccc
bbb: tty
tty: ccc
ccc: ddd eee
ddd: hub
hub: fff
eee: dac
dac: fff
fff: ggg hhh
ggg: out
hhh: out
`

type ConnnectionHash map[string][]string

func parse(i string) ConnnectionHash {
	list := ConnnectionHash{}
	for _, r := range utils.TrimSplit(i, "\n") {
		conn := strings.Fields(r)
		list[strings.Trim(conn[0], ":")] = conn[1:]
	}
	return list
}

func p1_visit_next(conn *ConnnectionHash, cache *map[string]int, node string) int {
	if node == "out" {
		return 1
	}
	if cached, ok := (*cache)[node]; ok {
		return cached
	}
	subtotal := 0
	for _, next := range (*conn)[node] {
		subtotal += p1_visit_next(conn, cache, next)
	}
	(*cache)[node] = subtotal
	return subtotal
}

func Part1(conn ConnnectionHash) int {
	cache := map[string]int{}
	paths := p1_visit_next(&conn, &cache, "you")
	return paths
}

func p2_visit_next(conn *ConnnectionHash, cache *map[string]int, node string, fft, dac bool) int {
	if node == "out" {
		if fft && dac {
			return 1
		} else {
			return 0
		}
	}
	fft = fft || node == "fft"
	dac = dac || node == "dac"
	key := node + "-" + strconv.FormatBool(fft) + "-" + strconv.FormatBool(dac)
	if paths, ok := (*cache)[key]; ok {
		return paths
	}
	subtotal := 0
	for _, next := range (*conn)[node] {
		subtotal += p2_visit_next(conn, cache, next, fft, dac)
	}
	(*cache)[key] = subtotal
	return subtotal
}

func Part2(conn ConnnectionHash) int {
	var cache = map[string]int{}
	paths := p2_visit_next(&conn, &cache, "svr", false, false)
	return paths
}

func main() {
	start := time.Now()

	connd := parse(inputdemo)
	connd2 := parse(inputdemo2)
	conni := parse(input)

	parsetime := time.Since(start)

	start1 := time.Now()

	var r1 int
	demo1expected := 5
	r1 = Part1(connd)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = Part1(conni)
	fmt.Printf("Part 1: %d\n", r1)

	p01time := time.Since(start1)

	//
	// Part 02
	//
	start2 := time.Now()

	var r2 int
	demo2expected := 2
	r2 = Part2(connd2)
	if r2 != demo2expected {
		panic(fmt.Sprintf("Part 2 demo failed: %d, expected %d", r2, demo2expected))
	}
	r2 = Part2(conni)
	fmt.Printf("Part 2: %d\n", r2)

	p02time := time.Since(start2)

	//
	// Output
	//
	fmt.Println()
	fmt.Println("Runtimes ↴")
	fmt.Printf("Parse        : %s\n", parsetime)
	fmt.Printf("Day 11 Part 1: %s\n", p01time)
	fmt.Printf("Day 11 Part 2: %s\n", p02time)
	fmt.Printf("Day 11 Total : %s\n", time.Since(start))
}
