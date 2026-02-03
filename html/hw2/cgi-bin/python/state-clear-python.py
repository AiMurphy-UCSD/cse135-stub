#!/usr/bin/env python3

COOKIE_NAME = "hw2_state"

print("Status: 302 Found")
print(f"Set-Cookie: {COOKIE_NAME}=deleted; Expires=Thu, 01 Jan 1970 00:00:01 GMT; Max-Age=0; Path=/; HttpOnly; SameSite=Lax")
print("Location: /cgi-bin/python/state-view-python.py")
print("Content-Type: text/html; charset=utf-8")
print()
