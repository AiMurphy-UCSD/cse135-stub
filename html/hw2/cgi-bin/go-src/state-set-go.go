package main

import (
	"fmt"
	"io"
	"net/url"
	"os"
)

func main() {
	method := os.Getenv("REQUEST_METHOD")
	rawBytes, _ := io.ReadAll(os.Stdin)
	raw := string(rawBytes)
	qs := os.Getenv("QUERY_STRING")

	var data url.Values
	if method == "GET" {
		data, _ = url.ParseQuery(qs)
	} else {
		data, _ = url.ParseQuery(raw)
	}

	value := data.Get("value")
	value = url.QueryEscape(value)

	fmt.Println("Status: 302 Found")
	fmt.Println("Set-Cookie: hw2_state=" + value + "; Path=/; HttpOnly; SameSite=Lax")
	fmt.Println("Location: /cgi-bin/go/state-view-go")
	fmt.Println()
}
