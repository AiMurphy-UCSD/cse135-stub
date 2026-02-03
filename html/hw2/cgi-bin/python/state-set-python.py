#!/usr/bin/env python3
import os
import urllib.parse
from datetime import datetime, timezone

COOKIE_NAME = "hw2_state"

def print_redirect(location: str, set_cookie: str | None = None):
    # CGI redirect: must print headers then blank line
    print("Status: 302 Found")
    if set_cookie:
        print(f"Set-Cookie: {set_cookie}")
    print(f"Location: {location}")
    print("Content-Type: text/html; charset=utf-8")
    print()

def print_html(body: str):
    print("Content-Type: text/html; charset=utf-8")
    print()
    print(body)

def get_param(name: str) -> str:
    qs = os.environ.get("QUERY_STRING", "")
    params = urllib.parse.parse_qs(qs, keep_blank_values=True)
    return params.get(name, [""])[0]

name = get_param("name").strip()

if not name:
    # Show a small form instead of clearing anything
    print_html(f"""<!doctype html>
<html><body>
<h1>Set State (Python)</h1>
<form method="GET" action="state-set-python.py">
  <label>Name: <input name="name"></label>
  <button type="submit">Save</button>
</form>
<p><a href="state-view-python.py">View state</a></p>
</body></html>""")
    raise SystemExit

# Set cookie visible to all endpoints
cookie_value = urllib.parse.quote(name, safe="")
set_cookie = f"{COOKIE_NAME}={cookie_value}; Path=/; HttpOnly; SameSite=Lax"

print_redirect("/cgi-bin/python/state-view-python.py", set_cookie=set_cookie)
