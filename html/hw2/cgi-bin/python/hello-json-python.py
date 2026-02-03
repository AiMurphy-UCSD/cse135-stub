#!/usr/bin/env python3
import os, json, datetime

resp = {
  "team": "Aidan Murphy",
  "language": "Python",
  "time_utc": datetime.datetime.utcnow().isoformat() + "Z",
  "ip": os.environ.get("REMOTE_ADDR", "unknown"),
}
print("Content-Type: application/json; charset=utf-8\r\n\r\n")
print(json.dumps(resp, indent=2))
