#!/usr/bin/env python3
import os, urllib.parse

cookie = os.environ.get("HTTP_COOKIE", "")
val = "(not set)"
for part in cookie.split(";"):
    part = part.strip()
    if part.startswith("hw2_state="):
        val = urllib.parse.unquote(part.split("=",1)[1])
        break

print("Content-Type: text/html; charset=utf-8\r\n\r\n")
print(f"""<!doctype html><html><body>
<h1>State (Python)</h1>
<p>Stored value: <b>{val}</b></p>

<form action="/cgi-bin/python/state-set-python.py" method="POST">
  <input name="value" placeholder="set value">
  <button type="submit">Save</button>
</form>

<p><a href="/cgi-bin/python/state-clear-python.py">Clear</a></p>
</body></html>""")
