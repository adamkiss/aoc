package main

import (
	"container/heap"
	"fmt"
	"regexp"
	"strconv"
	"strings"
	"time"

	"github.com/adamkiss/aoc/go/utils"
)

var input string = utils.ReadInput("10")
var inputdemo string = `
[.##.] (3) (1,3) (2) (2,3) (0,2) (0,1) {3,5,4,7}
[...#.] (0,2,3,4) (2,3) (0,4) (0,1,2) (1,2,3,4) {7,5,12,7,2}
[.###.#] (0,1,2,3,4) (0,3,4) (0,1,2,4,5) (1,2) {10,11,11,5,10,5}
`

var machine_re = regexp.MustCompile(`^\[([.#]*)\]\s*(\(.*\))\s*{([\d,]*)\}$`)
var buttons_re = regexp.MustCompile(`\(([\d,]*)\)`)

type QueueItem struct {
	//
	value int64
	steps []int
	//
	index int
}
type PriorityQueue []*QueueItem

func (pq PriorityQueue) Len() int {
	return len(pq)
}

func (pq PriorityQueue) Less(l, r int) bool {
	return len(pq[l].steps) < len(pq[r].steps)
}

func (pq PriorityQueue) Swap(l, r int) {
	pq[l], pq[r] = pq[r], pq[l]
	pq[l].index = l
	pq[r].index = r
}

func (pq *PriorityQueue) Push(x any) {
	n := len(*pq)
	i := x.(*QueueItem)
	i.index = n
	*pq = append(*pq, i)
}

func (pq *PriorityQueue) Pop() any {
	old := *pq
	n := len(old)
	i := old[n-1]
	old[n-1] = nil
	i.index = -1
	*pq = old[:n-1]
	return i
}

type Machine struct {
	target   int64
	buttons  []int64
	joltages []int
}

func findfastestsolution(m Machine) []int {
	q := make(PriorityQueue, 0)
	for i, b := range m.buttons {
		q.Push(&QueueItem{0 ^ b, []int{i}, i})
	}
	heap.Init(&q)

	for q.Len() > 0 {
		i := heap.Pop(&q).(*QueueItem)
		for bi, b := range m.buttons {
			nv := i.value ^ b
			st := append(i.steps, bi)
			if nv == int64(m.target) {
				return st
			} else {
				heap.Push(&q, &QueueItem{nv, st, q.Len()})
			}
		}
	}

	return []int{}
}

func ParseMachine(s string) Machine {
	matches := machine_re.FindStringSubmatch(s)

	tstr := strings.Replace(matches[1], ".", "0", -1)
	tstr = strings.Replace(tstr, "#", "1", -1)
	t, _ := strconv.ParseInt(tstr, 2, 64)

	buttons := []int64{}
	btns := buttons_re.FindAllStringSubmatch(matches[2], -1)
	for _, btnm := range btns {
		b := make([]rune, len(tstr))
		for i := range len(b) {
			b[i] = '0'
		}
		for _, whichbit := range strings.Split(btnm[1], ",") {
			bit, _ := strconv.Atoi(whichbit)
			b[bit] = '1'
		}
		btn, _ := strconv.ParseInt(string(b), 2, 64)
		buttons = append(buttons, btn)
	}

	joltagesraw := strings.Split(matches[3], ",")
	joltages := make([]int, len(joltagesraw))
	for i, jstr := range joltagesraw {
		j, _ := strconv.Atoi(jstr)
		joltages[i] = j
	}

	return Machine{
		target:   t,
		buttons:  buttons,
		joltages: joltages,
	}
}

func ParseInput(i string) []Machine {
	rawmachines := utils.TrimSplit(i, "\n")
	m := make([]Machine, len(rawmachines))
	for i, rm := range rawmachines {
		m[i] = ParseMachine(rm)
	}
	return m
}

func Part1(machines []Machine) int {
	var steps = 0
	for _, m := range machines {
		s := findfastestsolution(m)
		steps += len(s)
	}

	return steps
}

func Part2(i string) int {
	return 2
}

func main() {
	start := time.Now()

	machinesd := ParseInput(inputdemo)
	machinesi := ParseInput(input)

	parsetime := time.Since(start)

	start1 := time.Now()

	var r1 int
	demo1expected := 7
	r1 = Part1(machinesd)
	if r1 != demo1expected {
		panic(fmt.Sprintf("Part 1 demo failed: %d, expected %d", r1, demo1expected))
	}
	r1 = Part1(machinesi)
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
	fmt.Printf("Day 10 -Parse: %s\n", parsetime)
	fmt.Printf("Day 10 Part 1: %s\n", p01time)
	fmt.Printf("Day 10 Part 2: %s\n", p02time)
	fmt.Printf("Day 10 Total : %s\n", time.Since(start))
}
