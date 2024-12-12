package main

import (
	"fmt"
	"strconv"
	"strings"
	"time"
)

func setOrIncrease(m map[int]int, key int, value int) {
	if _, exists := m[key]; exists {
		m[key] += value
	} else {
		m[key] = value
	}
}

func partKeys(input string, blinks int) int {
	words := strings.Fields(input)

	n := make(map[int]int)
	for _, word := range words {
		num, err := strconv.Atoi(word)
		if err != nil {
			fmt.Println("Error converting string to int:", err)
			continue
		}
		setOrIncrease(n, num, 1)
	}

	for i := 0; i < blinks; i++ {
		n2 := make(map[int]int)
		for v, c := range n {
			if v == 0 {
				setOrIncrease(n2, 1, c)
			} else if len(strconv.Itoa(v))%2 == 0 {
				vStr := strconv.Itoa(v)
				l := len(vStr) / 2
				firstHalf, _ := strconv.Atoi(vStr[:l])
				secondHalf, _ := strconv.Atoi(vStr[l:])
				setOrIncrease(n2, firstHalf, c)
				setOrIncrease(n2, secondHalf, c)
			} else {
				setOrIncrease(n2, v*2024, c)
			}
		}
		n = n2
	}

	sum := 0
	for _, counts := range n {
		sum += counts
	}
	return sum
}

func main() {
	start := time.Now()
	input := "6571 0 5851763 526746 23 69822 9 989"
	fmt.Println(partKeys("125 17", 6))
	fmt.Println(partKeys("125 17", 22))
	fmt.Println(partKeys(input, 25))
	fmt.Printf("p1 took %s", time.Since(start))
	fmt.Println()

	fmt.Println(partKeys(input, 75))
	fmt.Printf("p1+p2 took %s", time.Since(start))
}
