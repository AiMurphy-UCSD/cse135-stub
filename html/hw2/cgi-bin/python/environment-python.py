#!/usr/bin/env python3
import os
print("Content-Type: text/plain; charset=utf-8\r\n\r\n")
for k, v in sorted(os.environ.items()):
    print(f"{k}={v}")
