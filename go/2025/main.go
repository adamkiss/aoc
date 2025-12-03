package main

import (
	"fmt"
	"os"
	"time"

	"github.com/adamkiss/aoc/2025/days"
)

func main() {
	start := time.Now().Local().UnixMicro()
	args := os.Args[1:]
	days := map[string]func(){
		"01": days.Day01,
	}

	var dayfunc func()
	if len(args) == 0 {
		dayfunc = days["01"]
	} else {
		if _, ok := days[args[0]]; !ok {
			fmt.Printf("Invalid day '%s'\n", args[0])
			os.Exit(1)
		}
		dayfunc = days[args[0]]
	}

	dayfunc()
	fmt.Printf("Total: %.3fms", float64(time.Now().Local().UnixMicro()-start)/1000)
}
