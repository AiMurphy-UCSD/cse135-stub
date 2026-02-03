#!/usr/bin/env python3
import os
import urllib.parse

COOKIE_NAME = "hw2_state"

def parse_cookies(cookie_header: str) -> dict:
    out = {}
    for part in cookie_header.split(";"):
        part = part.strip()
        if "=" in part:
            k, v = part.split("=", 1)
            out[k] = v
    return out

cookie_header = os.environ.get("HTTP_COOKIE", "")
cookies = parse_cookies(cookie_header)
raw = cookies.get(COOKIE_NAME, "")
value = urllib.parse.unquote(raw) if raw else ""

print("Content-Type: text/html; charset=utf-8")
print()
print(f"""<!doctype html>
<html><body>
<h1>State View (Python)</h1>
{"<p>Saved: <b>" + value + "</b></p>" if value else "<p>No state saved.</p>"}
<p>
  <a href="state-set-python.py">Set</a> |
  <a href="state-clear-python.py">Clear</a>
</p>
</body></html>""")
