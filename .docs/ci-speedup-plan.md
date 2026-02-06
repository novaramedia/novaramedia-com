# CI Speedup Plan

## One-Time Server Setup (REQUIRED)

The CI workflow deploys by running `git fetch` + `git checkout` over SSH on the Kinsta staging server. For this to work, the theme directory on the server needs to be a git clone of the repo with read access to GitHub.

This setup only needs to be done once. After that, the CI workflow handles everything automatically.

### Prerequisites

- SSH access to the Kinsta **staging** environment
- Your SSH credentials from MyKinsta (Settings > SFTP/SSH on the staging environment)
- Admin access to the GitHub repo (to add a deploy key)

### Step 1: Find your Kinsta SSH credentials

1. Log into [MyKinsta](https://my.kinsta.com/)
2. Go to **Sites** > select the site > **Info** tab
3. Make sure you're looking at the **Staging** environment (toggle at top)
4. Under **SFTP/SSH**, note these values:
   - **Host** (e.g., `ssh.kinsta.cloud` or similar)
   - **Port** (Kinsta uses non-standard ports, e.g., `12345`)
   - **Username** (e.g., `ssh-user-abc123`)

### Step 2: SSH into the staging server

```bash
ssh -p PORT USERNAME@HOST
```

Replace `PORT`, `USERNAME`, and `HOST` with the values from Step 1.

Once connected, verify git is available:

```bash
which git && git --version
```

You should see something like `/usr/bin/git` and `git version 2.x.x`.

### Step 3: Generate a deploy key on the server

While still SSH'd into the server, generate an SSH keypair that git will use to talk to GitHub:

```bash
ssh-keygen -t ed25519 -C "kinsta-staging-deploy" -f ~/.ssh/github_deploy_key -N ""
```

This creates two files:

- `~/.ssh/github_deploy_key` (private key — stays on the server)
- `~/.ssh/github_deploy_key.pub` (public key — goes to GitHub)

Configure SSH to use this key for GitHub connections:

```bash
cat >> ~/.ssh/config << 'EOF'
Host github.com
  HostName github.com
  User git
  IdentityFile ~/.ssh/github_deploy_key
  IdentitiesOnly yes
EOF
chmod 600 ~/.ssh/config
```

Print the public key — you'll need it in the next step:

```bash
cat ~/.ssh/github_deploy_key.pub
```

Copy the output (starts with `ssh-ed25519 ...`).

### Step 4: Add the deploy key to GitHub

1. Go to https://github.com/novaramedia/novaramedia-com/settings/keys
2. Click **Add deploy key**
3. **Title:** `Kinsta Staging`
4. **Key:** Paste the public key from Step 3
5. **Allow write access:** Leave unchecked (read-only is sufficient)
6. Click **Add key**

### Step 5: Test GitHub access from the server

Back on the server SSH session:

```bash
ssh -T git@github.com
```

You should see: `Hi novaramedia/novaramedia-com! You've successfully authenticated...`

If you see `Permission denied`, double-check the deploy key was added correctly and the `~/.ssh/config` file is correct.

### Step 6: Replace the theme directory with a git clone

```bash
cd ~/public/wp-content/themes

# Back up the current theme directory
mv novaramedia-com novaramedia-com.bak

# Clone the repo (uses the deploy key via SSH)
git clone git@github.com:novaramedia/novaramedia-com.git

# Switch to the development branch
cd novaramedia-com
git checkout development
```

Verify the site still works by visiting the staging URL in your browser. If something is broken, you can roll back immediately:

```bash
cd ~/public/wp-content/themes
rm -rf novaramedia-com
mv novaramedia-com.bak novaramedia-com
```

### Step 7: Verify git operations work

Run the same commands the CI workflow will use:

```bash
cd ~/public/wp-content/themes/novaramedia-com

# This is what the deploy step does:
git fetch origin
git checkout development
git reset --hard origin/development
```

All three commands should complete without errors or password prompts.

### Step 8: Clean up and exit

Once everything is confirmed working:

```bash
rm -rf ~/public/wp-content/themes/novaramedia-com.bak
exit
```

### Step 9: Update GitHub Secrets

Go to https://github.com/novaramedia/novaramedia-com/settings/secrets/actions

**Ensure these secrets exist** (the workflow needs them):

| Secret            | Value                                                               | Notes                                                                                  |
| ----------------- | ------------------------------------------------------------------- | -------------------------------------------------------------------------------------- |
| `KINSTA_SSH_KEY`  | SSH private key for CI to connect to Kinsta                         | Same key used before for WP-CLI                                                        |
| `KINSTA_SSH_USER` | SSH username from Step 1                                            | Was previously `KINSTA_SSH_USER` — no change needed                                    |
| `KINSTA_SSH_HOST` | SSH host from Step 1                                                | If you previously had `KINSTA_SFTP_HOST`, create `KINSTA_SSH_HOST` with the same value |
| `KINSTA_SSH_PORT` | SSH port from Step 1                                                | Same as before                                                                         |
| `STAGING_URL`     | Full staging URL (e.g., `https://staging-novaramedia.kinsta.cloud`) | Same as before                                                                         |

**Optional but recommended** (for cache clearing):

| Secret                  | Value                                               |
| ----------------------- | --------------------------------------------------- |
| `KINSTA_API_KEY`        | Kinsta API key (from MyKinsta > Company > API Keys) |
| `KINSTA_SITE_ID`        | Site ID (visible in MyKinsta URL or via API)        |
| `KINSTA_STAGING_ENV_ID` | Staging environment ID (via Kinsta API)             |

**Can be removed** (no longer needed):

- `KINSTA_SFTP_USER`
- `KINSTA_SFTP_PASSWORD`
- `KINSTA_SFTP_PORT`

### Step 10: Verify CI works

Trigger a workflow run to confirm everything is connected:

1. Go to https://github.com/novaramedia/novaramedia-com/actions/workflows/cypress.yml
2. Click **Run workflow** > select the branch > **Run workflow**
3. Watch the "Deploy PR branch to staging" step — it should complete in ~10-15 seconds

### Security: Verify .git is not web-accessible

The `.git` directory will exist on the staging server. Kinsta blocks dotfile access at the nginx level by default, but verify this:

```bash
curl -s -o /dev/null -w "%{http_code}" https://YOUR-STAGING-URL/.git/HEAD
```

This should return `403` or `404`. If it returns `200`, contact Kinsta support to block dotfile access.

### Troubleshooting

**`Permission denied (publickey)`** when running `git fetch`:

- Check `~/.ssh/config` exists and points to the correct key file
- Check the deploy key is added to the GitHub repo (not your personal account)
- Run `ssh -vT git@github.com` for verbose debugging output

**`fatal: not a git repository`** during CI:

- The theme directory wasn't replaced with a git clone
- SSH into the server and check: `cd ~/public/wp-content/themes/novaramedia-com && git status`

**CI deploy step hangs:**

- Git might be prompting for credentials (deploy key not configured)
- SSH into the server manually and run `git fetch origin` to see what happens

---

## Background: Why Git Deploy?

The previous SFTP-based workflow mirrored the entire theme directory (~50MB) twice per run — once to deploy, once to reset. This took ~15 min of the ~18 min total CI time (88%).

| Before (SFTP)           | After (git)            |
| ----------------------- | ---------------------- |
| SFTP deploy: ~10-12 min | git deploy: ~10-15 sec |
| SFTP cleanup: ~5 min    | git reset: ~10 sec     |
| **Total: ~18 min**      | **Total: ~4 min**      |

## Future Optimisations

- **Cypress binary caching:** Cache `~/.cache/Cypress` between runs (saves ~10 sec)
- **Remove temporary push trigger:** The `push: branches: [copilot/add-cypress-testing-ci]` trigger should be removed after merging to development
