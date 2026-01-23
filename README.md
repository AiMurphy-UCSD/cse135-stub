Github Deployment:
This project uses a GitHub webhook-based deployment pipeline. All site code lives in a GitHub repository. The server maintains its own clone of the repository in /var/repo/. When code is pushed to GitHub, a webhook sends a signed HTTPS request to the server. 
Apache forwards this request to a local Flask webhook listener, which verifies the GitHub signature and triggers a deployment script. The deployment script performs a git pull and synchronizes the updated files into the Apache document roots using rsync. 
This allows fully automatic deployment from GitHub to the live site without manual server edits.
