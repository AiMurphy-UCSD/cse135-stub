package main

import (
	"encoding/json"
	"fmt"
	"os"
	"time"
)

func main() {
	resp := map[string]any{
		"team":     "Aidan Murphy",
		"language": "Go",
		"time_utc": time.Now().UTC().Format(time.RFC3339),
		"ip":       getenv("REMOTE_ADDR", "unknown"),
	}

	fmt.Print("Content-Type: application/json; charset=utf-8\r\n\r\n")
	enc := json.NewEncoder(os.Stdout)
	enc.SetIndent("", "  ")
	_ = enc.Encode(resp)
}

func getenv(k, def string) string {
	v := os.Getenv(k)
	if v == "" { return def }
	return v
}
