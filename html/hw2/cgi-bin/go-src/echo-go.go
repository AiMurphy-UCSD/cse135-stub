package main

import (
	"encoding/json"
	"fmt"
	"io"
	"net/url"
	"os"
	"strings"
	"time"
)

func main() {
	method := getenv("REQUEST_METHOD", "GET")
	ct := os.Getenv("CONTENT_TYPE")
	ip := getenv("REMOTE_ADDR", "unknown")
	ua := getenv("HTTP_USER_AGENT", "unknown")
	host := getenv("HTTP_HOST", "unknown")
	now := time.Now().UTC().Format(time.RFC3339)

	qs := os.Getenv("QUERY_STRING")
	queryMap := parseQuery(qs)

	rawBytes, _ := io.ReadAll(os.Stdin)
	raw := string(rawBytes)

	var body any = nil
	if len(strings.TrimSpace(raw)) > 0 {
		if strings.Contains(ct, "application/json") {
			var v any
			if err := json.Unmarshal([]byte(raw), &v); err != nil {
				body = map[string]any{"_error": "invalid json", "_raw": raw}
			} else {
				body = v
			}
		} else {
			body = parseQuery(raw)
		}
	}

	resp := map[string]any{
		"host": host, "time_utc": now, "method": method,
		"content_type": ct, "ip": ip, "user_agent": ua,
		"query": queryMap, "body": body, "raw_body": raw,
	}

	fmt.Print("Content-Type: application/json; charset=utf-8\r\n\r\n")
	enc := json.NewEncoder(os.Stdout)
	enc.SetIndent("", "  ")
	_ = enc.Encode(resp)
}

func parseQuery(q string) map[string]any {
	m := map[string]any{}
	vals, _ := url.ParseQuery(q)
	for k, v := range vals {
		if len(v) == 1 { m[k] = v[0] } else { m[k] = v }
	}
	return m
}

func getenv(k, def string) string {
	v := os.Getenv(k)
	if v == "" { return def }
	return v
}
