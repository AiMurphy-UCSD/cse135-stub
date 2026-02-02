#!/usr/bin/env python3
import os
import datetime

team = "Aidan Murphy"
lang = "Python"
time = datetime.datetime.utcnow().isoformat() + "Z"
ip = os.environ.get("REMOTE_ADDR", "unknown")

print("Content-Type: text/html; charset=utf-8\r\n\r\n")
print(f"""<!doctype html><html><body>
<h1>Hello (HTML)</h1>
<ul>
  <li>Team: {team}</li>
  <li>Language: {lang}</li>
  <li>Time (UTC): {time}</li>
  <li>IP: {ip}</li>
</ul>
</body></html>""")
