package main

import "fmt"

const cookieName = "hw2_state"

func main() {
	fmt.Print("Status: 302 Found\r\n")
	fmt.Printf("Set-Cookie: %s=deleted; Expires=Thu, 01 Jan 1970 00:00:01 GMT; Max-Age=0; Path=/; HttpOnly; SameSite=Lax\r\n", cookieName)
	fmt.Print("Location: /cgi-bin/go/state-view-go\r\n")
	fmt.Print("Content-Type: text/html; charset=utf-8\r\n\r\n")
}
