package utils

import (
	"fmt"
	"os"
	"path"
	"strings"
)

func ReadInput(file string) string {
	cwd, err := os.Getwd()
	if err != nil {
		panic(err)
	}

	root := strings.Split(cwd, "aoc")[0] + "aoc/inputs/2025/"
	if !strings.Contains(file, ".") {
		file = fmt.Sprintf("%s.txt", file)
	}

	content, err := os.ReadFile(path.Join(root, file))
	if err != nil {
		panic(err)
	}
	return string(content)
}

func ReadInputBytes(file string) []byte {
	cwd, err := os.Getwd()
	if err != nil {
		panic(err)
	}

	root := strings.Split(cwd, "aoc")[0] + "aoc/inputs/2025/"
	if !strings.Contains(file, ".") {
		file = fmt.Sprintf("%s.txt", file)
	}

	content, err := os.ReadFile(path.Join(root, file))
	if err != nil {
		panic(err)
	}
	return content
}

func TrimSplit(s string, chr string) []string {
	s = strings.TrimSpace(s)
	lines := strings.Split(s, chr)
	var trimmed []string
	for _, line := range lines {
		t := strings.TrimSpace(line)
		if t == "" {
			continue
		}
		trimmed = append(trimmed, t)
	}
	return trimmed
}
