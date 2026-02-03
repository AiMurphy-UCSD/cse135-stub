package main

import "fmt"

const cookieName = "hw2_state"

func main() {
	fmt.Print("Content-Type: text/html; charset=utf-8\r\n")
	fmt.Printf("Set-Cookie: %s=deleted; Expires=Thu, 01 Jan 1970 00:00:01 GMT; Max-Age=0; Path=/; HttpOnly; SameSite=Lax\r\n", cookieName)
	fmt.Print("\r\n")

	fmt.Print(`<!doctype html><html><body>
<h1>Clear State (Go)</h1>
<p>State cleared.</p>
<p>
  <a href="state-set-go">Set State</a> |
  <a href="state-view-go">View State</a>
</p>
</body></html>`)
}
