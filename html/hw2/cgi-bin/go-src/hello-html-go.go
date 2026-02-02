package main

import (
	"encoding/json"
	"fmt"
	"os"
	"time"
)

func main() {
	team := "Aidan Murphy"
	lang := "Go"
	ip := os.Getenv("REMOTE_ADDR")
	if ip == "" { ip = "unknown" }

	fmt.Print("Content-Type: text/html; charset=utf-8\r\n\r\n")
	fmt.Printf(`<!doctype html><html><body>
<h1>Hello (HTML)</h1>
<ul>
  <li>Team: %s</li>
  <li>Language: %s</li>
  <li>Time (UTC): %s</li>
  <li>IP: %s</li>
</ul>
</body></html>`, team, lang, time.Now().UTC().Format(time.RFC3339), ip)

	_ = json.NewEncoder // keep import patterns simple if you expand later
}
