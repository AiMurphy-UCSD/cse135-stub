package main

import (
	"fmt"
	"net/url"
	"os"
	"strings"
)

const cookieName = "hw2_state"

func parseCookies(h string) map[string]string {
	out := map[string]string{}
	parts := strings.Split(h, ";")
	for _, p := range parts {
		p = strings.TrimSpace(p)
		if eq := strings.Index(p, "="); eq != -1 {
			out[p[:eq]] = p[eq+1:]
		}
	}
	return out
}

func main() {
	cookieHeader := os.Getenv("HTTP_COOKIE")
	cookies := parseCookies(cookieHeader)
	raw := cookies[cookieName]
	val, _ := url.QueryUnescape(raw)

	fmt.Print("Content-Type: text/html; charset=utf-8\r\n\r\n")
	fmt.Print("<!doctype html><html><body>")
	fmt.Print("<h1>State View (Go)</h1>")
	if raw != "" {
		fmt.Printf("<p>Saved: <b>%s</b></p>", val)
	} else {
		fmt.Print("<p>No state saved.</p>")
	}
	fmt.Print(`<p>
  <a href="state-set-go">Set</a> |
  <a href="state-clear-go">Clear</a>
</p>`)
	fmt.Print("</body></html>")
}
