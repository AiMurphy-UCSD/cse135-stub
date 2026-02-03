package main

import (
	"fmt"
	"net/url"
	"os"
	"strings"
)

func main() {
	cookie := os.Getenv("HTTP_COOKIE")
	val := "(not set)"
	for _, part := range strings.Split(cookie, ";") {
		part = strings.TrimSpace(part)
		if strings.HasPrefix(part, "hw2_state=") {
			val = strings.TrimPrefix(part, "hw2_state=")
			decoded, _ := url.QueryUnescape(val)
			val = decoded
		}
	}

	fmt.Print("Content-Type: text/html; charset=utf-8\r\n\r\n")
	fmt.Printf(`<!doctype html><html><body>
<h1>State (Go)</h1>
<p>Stored value: <b>%s</b></p>
<form action="/cgi-bin/go/state-set-go" method="POST">
  <input name="value" placeholder="set value">
  <button type="submit">Save</button>
</form>
<p><a href="/cgi-bin/go/state-clear-go">Clear</a></p>
</body></html>`, val)
}
