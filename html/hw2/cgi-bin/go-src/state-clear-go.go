package main

import "fmt"

func main() {
	fmt.Println("Status: 302 Found")
	fmt.Println("Set-Cookie: hw2_state=; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; HttpOnly; SameSite=Lax")
	fmt.Println("Location: /cgi-bin/go/state-view-go")
	fmt.Println()
}
