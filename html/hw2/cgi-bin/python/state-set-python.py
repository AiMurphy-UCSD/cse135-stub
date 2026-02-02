#!/usr/bin/env python3
import os, sys, urllib.parse

method = os.environ.get("REQUEST_METHOD", "GET")
length = int(os.environ.get("CONTENT_LENGTH") or 0)
raw = sys.stdin.read(length) if length > 0 else ""
qs = os.environ.get("QUERY_STRING", "")

data = {}
if method == "GET":
    data = urllib.parse.parse_qs(qs)
else:
    data = urllib.parse.parse_qs(raw)

value = (data.get("value", [""])[0])

print("Status: 302 Found")
print("Set-Cookie: hw2_state=" + urllib.parse.quote(value) + "; Path=/; HttpOnly; SameSite=Lax")
print("Location: /cgi-bin/python/state-view-python.py")
print()
