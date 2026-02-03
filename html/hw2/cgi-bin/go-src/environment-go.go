package main

import (
	"fmt"
	"os"
	"sort"
)

func main() {
	fmt.Print("Content-Type: text/plain; charset=utf-8\r\n\r\n")
	env := os.Environ()
	sort.Strings(env)
	for _, kv := range env {
		fmt.Println(kv)
	}
}
