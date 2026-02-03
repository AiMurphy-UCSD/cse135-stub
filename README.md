## Members
- Aidan Murphy

## Apache Login for Grader
- **Username:** grader  
- **Password:** UCSD1234

## Link to Site
- https://aidanmurphy.site/

## GitHub Deployment
This project uses a GitHub webhook-based deployment pipeline. All site code lives in a GitHub repository. The server maintains its own clone of the repository in `/var/repo/`. When code is pushed to GitHub, a webhook sends a signed HTTPS request to the server.

Apache forwards this request to a local Flask webhook listener, which verifies the GitHub signature and triggers a deployment script. The deployment script performs a `git pull` and synchronizes the updated files into the Apache document roots using `rsync`.

This allows fully automatic deployment from GitHub to the live site without manual server edits.

## Team Stub Site Login
- **Username:** grader  
- **Password:** 1234

## Compression Verification (mod_deflate)
I enabled Apache’s `mod_deflate` so that textual assets are served using gzip compression. In Chrome DevTools, my HTML/CSS responses include the header `Content-Encoding: gzip`, confirming the server is compressing them. DevTools also shows that the **Transferred** size is smaller than the **Resource** size for these files, indicating that compressed data was sent over the network and decompressed by the browser.

## Obscure Server Identity
I initially attempted to override the `Server` header using Apache `mod_headers`, but Apache still returned `Server: Apache/2.4.58 (Ubuntu)` because the Server header is generated internally and isn’t reliably overridden via `Header set`. I then installed `mod_security2` and configured  
`SecServerSignature "CSE135 Server"`  
to replace the server banner. Verification using curl and Chrome DevTools confirms that responses now include:

## Free Choice Analytics (GoatCounter)
GoatCounter is a privacy-first, lightweight analytics platform designed as an alternative to heavy, invasive trackers like Google Analytics. Its most unique feature is that it does not use cookies, does not fingerprint users, and does not track individuals across sites, making it compliant with privacy regulations (such as GDPR) by default.

Unlike session-replay or behavior-profiling tools, GoatCounter focuses on simple, aggregate metrics—page views, referrers, browsers, and basic usage trends—without collecting personal data. It also has a very small script size, minimal performance impact, and can be self-hosted, which gives developers full control over their analytics data.

Overall, GoatCounter is a strong choice when privacy, transparency, simplicity, and low overhead are more important than deep behavioral tracking, making it a good contrast to tools like Google Analytics and LogRocket.








