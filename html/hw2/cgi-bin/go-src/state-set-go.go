package main

import (
	"fmt"
	"net/url"
	"os"
	"strings"
)

const cookieName = "hw2_state"

func main() {
	qs := os.Getenv("QUERY_STRING")
	values, _ := url.ParseQuery(qs)
	name := strings.TrimSpace(values.Get("name"))

	// Always HTML page
	fmt.Print("Content-Type: text/html; charset=utf-8\r\n")

	// If a name is provided, set the cookie (still no redirect)
	if name != "" {
		escaped := url.QueryEscape(name)
		fmt.Printf("Set-Cookie: %s=%s; Path=/; HttpOnly; SameSite=Lax\r\n", cookieName, escaped)
	}

	fmt.Print("\r\n") // end headers

	fmt.Print("<!doctype html><html><body>")
	fmt.Print("<h1>Set State (Go)</h1>")

	if name != "" {
		fmt.Printf("<p>Saved: <b>%s</b></p>", name)
	} else {
		fmt.Print("<p>Enter a value to save in a cookie.</p>")
	}

	fmt.Print(`
<form method="GET" action="state-set-go">
  <label>Name: <input name="name"></label>
  <button type="submit">Save</button>
</form>

<p>
  <a href="state-view-go">View State</a> |
  <a href="state-clear-go">Clear State</a>
</p>
</body></html>`)
}
