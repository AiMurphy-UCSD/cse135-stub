package main

import (
	"fmt"
	"net/url"
	"os"
)

const cookieName = "hw2_state"

func main() {
	qs := os.Getenv("QUERY_STRING")
	values, _ := url.ParseQuery(qs)
	name := values.Get("name")

	if name == "" {
		fmt.Print("Content-Type: text/html; charset=utf-8\r\n\r\n")
		fmt.Print(`<!doctype html><html><body>
<h1>Set State (Go)</h1>
<form method="GET" action="state-set-go">
  <label>Name: <input name="name"></label>
  <button type="submit">Save</button>
</form>
<p><a href="state-view-go">View state</a></p>
</body></html>`)
		return
	}

	escaped := url.QueryEscape(name)

	fmt.Print("Status: 302 Found\r\n")
	fmt.Printf("Set-Cookie: %s=%s; Path=/; HttpOnly; SameSite=Lax\r\n", cookieName, escaped)
	fmt.Print("Location: /cgi-bin/go/state-view-go\r\n")
	fmt.Print("Content-Type: text/html; charset=utf-8\r\n\r\n")
}
