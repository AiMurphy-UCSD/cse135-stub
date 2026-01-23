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



![initial-index](https://github.com/user-attachments/assets/acf61d22-aae0-46ea-9d08-038c1a506531)
![modified-index](https://github.com/user-attachments/assets/b8db685a-e4a4-4797-8134-3c1f138ec06a)
![validator-initial](https://github.com/user-attachments/assets/b49bb8a9-2a73-4afb-844b-bfcc4c87ee58)
![vhosts-verify](https://github.com/user-attachments/assets/af81a8a1-ec5b-4e2e-bf9a-117e19df5126)
![SSL-verify](https://github.com/user-attachments/assets/13c7b597-2d3f-4b92-af10-ac66dab557f8)
https://github.com/user-attachments/assets/fefdb9b3-af04-4d56-84be-9ff05da97435
![php-verification](https://github.com/user-attachments/assets/465df2e2-35ec-4174-ad01-e19d95f42c28)
![compress-verify](https://github.com/user-attachments/assets/2f8a44f4-2286-45b2-8c63-0d8a31121238)
![header-verify](https://github.com/user-attachments/assets/bd3b511d-141c-477e-aede-bafce7f139a1)
![error-page](https://github.com/user-attachments/assets/c1347459-c046-4f32-b597-01d5a635b834)
![log-verification](https://github.com/user-attachments/assets/864d5456-4386-4334-a88a-25cf4c9e1e5e)
![report-verification](https://github.com/user-attachments/assets/177e66af-98dd-4434-a8a3-ed141168251f)








