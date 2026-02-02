#!/usr/bin/env python3
import os, sys, json, urllib.parse, datetime, socket

method = os.environ.get("REQUEST_METHOD", "GET")
content_type = os.environ.get("CONTENT_TYPE", "")
ip = os.environ.get("REMOTE_ADDR", "unknown")
ua = os.environ.get("HTTP_USER_AGENT", "unknown")
host = os.environ.get("HTTP_HOST", socket.gethostname())
time = datetime.datetime.utcnow().isoformat() + "Z"

query = urllib.parse.parse_qs(os.environ.get("QUERY_STRING", ""), keep_blank_values=True)
# flatten query values for nicer output
query = {k: (v[0] if len(v)==1 else v) for k, v in query.items()}

length = int(os.environ.get("CONTENT_LENGTH") or 0)
raw = sys.stdin.read(length) if length > 0 else ""

body = None
if raw:
    if "application/json" in content_type:
        try:
            body = json.loads(raw)
        except Exception:
            body = {"_error": "invalid json", "_raw": raw}
    else:
        parsed = urllib.parse.parse_qs(raw, keep_blank_values=True)
        body = {k: (v[0] if len(v)==1 else v) for k, v in parsed.items()}

resp = {
    "host": host,
    "time_utc": time,
    "method": method,
    "content_type": content_type,
    "ip": ip,
    "user_agent": ua,
    "query": query,
    "body": body,
    "raw_body": raw,
}

print("Content-Type: application/json; charset=utf-8\r\n\r\n")
print(json.dumps(resp, indent=2))
