## Github Deployment:
<br>
This project uses a GitHub webhook-based deployment pipeline. All site code lives in a GitHub repository. The server maintains its own clone of the repository in /var/repo/. When code is pushed to GitHub, a webhook sends a signed HTTPS request to the server. 
Apache forwards this request to a local Flask webhook listener, which verifies the GitHub signature and triggers a deployment script. The deployment script performs a git pull and synchronizes the updated files into the Apache document roots using rsync. 
This allows fully automatic deployment from GitHub to the live site without manual server edits.
<br>
## Team Stub Site Login:
<br>
username: grader
password: UCSD1234
<br>
## Compression Verification (mod_deflate)
<br>
I enabled Apache’s mod_deflate so that textual assets are served using gzip compression. In Chrome DevTools, my HTML/CSS responses include the header Content-Encoding: gzip, confirming the server is compressing them. DevTools also shows that the Transferred size is smaller than the Resource size for these files, indicating that compressed data was sent over the network and decompressed by the browser.
