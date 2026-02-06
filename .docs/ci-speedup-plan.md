# CI Speedup Plan

## One-Time Server Setup (REQUIRED)

The workflow uses `git pull` over SSH to deploy. The theme directory on Kinsta staging needs to be a git clone.

### Steps

1. **SSH into the Kinsta staging server:**

   ```bash
   ssh -p $PORT $USER@$HOST
   ```

2. **Back up and replace the theme directory with a git clone:**

   ```bash
   cd public/wp-content/themes
   mv novaramedia-com novaramedia-com.bak
   git clone https://github.com/novaramedia/novaramedia-com.git
   cd novaramedia-com
   git checkout development
   ```

3. **Set up GitHub access on the server** (one of these options):
   - **Option A: Deploy key (recommended)** — Generate an SSH key on the server, add the public key as a read-only deploy key in GitHub repo settings (Settings > Deploy keys). Then set the clone URL to SSH: `git remote set-url origin git@github.com:novaramedia/novaramedia-com.git`
   - **Option B: HTTPS with token** — Create a fine-grained personal access token with read-only repo access, configure it: `git remote set-url origin https://<token>@github.com/novaramedia/novaramedia-com.git`

4. **Verify it works:**

   ```bash
   git fetch origin
   git checkout development
   git pull origin development
   ```

5. **Clean up backup once confirmed working:**

   ```bash
   rm -rf /path/to/themes/novaramedia-com.bak
   ```

6. **Update GitHub Secrets** — The secret names changed slightly:
   - `KINSTA_SSH_HOST` (was `KINSTA_SFTP_HOST`) — can be the same value
   - `KINSTA_SSH_USER`, `KINSTA_SSH_KEY`, `KINSTA_SSH_PORT` — same as before
   - Remove: `KINSTA_SFTP_USER`, `KINSTA_SFTP_PASSWORD`, `KINSTA_SFTP_PORT` (no longer needed)

### Security Note

The `.git` directory will exist on the server. Ensure it's not web-accessible. Kinsta typically blocks dotfile access at the nginx level, but verify by visiting `https://staging-site.kinsta.cloud/.git/HEAD` — it should return 403/404, not the file contents.

---

## Current State (before optimisation)

| Step                        | Duration    | % of total |
| --------------------------- | ----------- | ---------- |
| SFTP deploy (PR branch)     | ~10-12 min  | 60%        |
| WP-CLI + cache clear        | ~30 sec     | 3%         |
| npm ci                      | ~25 sec     | 2%         |
| Cypress tests               | ~2 min      | 11%        |
| SFTP cleanup (reset to dev) | ~5 min      | 28%        |
| **Total**                   | **~18 min** |            |

The SFTP mirror transfers the entire theme directory (~50MB with dist/) on every run, twice — once to deploy, once to reset. This accounts for ~88% of the total time.

## Plan: Two Tiers

### Tier 1: Git Pull on Server (target: ~4 min total)

**Prerequisite:** Git is available on Kinsta staging SSH. Needs a one-time check.

If git is available, we replace the entire SFTP workflow with:

```
1. SSH into server
2. cd to theme directory
3. git fetch origin
4. git checkout <PR branch>
5. git pull
```

**Projected times:**

| Step                                     | Duration   |
| ---------------------------------------- | ---------- |
| SSH + git pull (deploy)                  | ~10-15 sec |
| WP-CLI + cache clear                     | ~30 sec    |
| npm ci                                   | ~25 sec    |
| Cypress tests                            | ~2 min     |
| SSH + git checkout development (cleanup) | ~10 sec    |
| **Total**                                | **~4 min** |

**Implementation:**

1. **One-time setup:** Clone the repo into the theme directory on staging, or set the existing directory as a git working tree. This requires the server to have read access to the GitHub repo (deploy key or HTTPS token).

2. **Deploy step becomes:**

```yaml
- name: Deploy PR branch to staging
  run: |
    ssh -i ~/.ssh/kinsta_key -p ${{ secrets.KINSTA_SSH_PORT }} \
      ${{ secrets.KINSTA_SSH_USER }}@${{ secrets.KINSTA_SFTP_HOST }} \
      "cd public/wp-content/themes/novaramedia-com && \
       git fetch origin && \
       git checkout ${{ github.head_ref || github.ref_name }} && \
       git pull origin ${{ github.head_ref || github.ref_name }}"
```

3. **Cleanup step becomes:**

```yaml
- name: Reset staging to development
  run: |
    ssh -i ~/.ssh/kinsta_key -p ${{ secrets.KINSTA_SSH_PORT }} \
      ${{ secrets.KINSTA_SSH_USER }}@${{ secrets.KINSTA_SFTP_HOST }} \
      "cd public/wp-content/themes/novaramedia-com && \
       git checkout development && \
       git pull origin development"
```

4. **GitHub Secrets needed:**
   - `KINSTA_GIT_TOKEN` — GitHub personal access token or deploy key for the repo (so the server can `git fetch`)
   - Or: add a read-only deploy key to the repo and install it on the server

**Risks:**

- Server might not have git
- Need to manage git credentials on the server
- `.git` directory exists on the server (small security consideration — ensure it's not web-accessible)

---

### Tier 2: Optimised SFTP (target: ~8-10 min total)

If git is not available, optimise the current SFTP approach:

#### 2a. Remove cleanup step (saves ~5 min immediately)

Staging stays on the PR branch between runs. Since there's a concurrency group, it always gets overwritten by the next run anyway.

**Projected total: ~13 min → saving 5 min**

#### 2b. Diff-based upload (saves ~8-10 min on deploy)

Instead of mirroring the entire directory, only upload files that changed:

```yaml
- name: Deploy changed files to staging
  run: |
    # Get list of changed files compared to what's on staging (development)
    CHANGED_FILES=$(git diff --name-only development...HEAD)
    DELETED_FILES=$(git diff --name-only --diff-filter=D development...HEAD)

    # Upload only changed files via SFTP
    for file in $CHANGED_FILES; do
      lftp -c "
        open -u $USER,$PASS sftp://$HOST:$PORT
        put $file -o novaramedia-com/$file
      "
    done

    # Delete removed files
    for file in $DELETED_FILES; do
      lftp -c "
        open -u $USER,$PASS sftp://$HOST:$PORT
        rm novaramedia-com/$file
      "
    done
```

**Projected times:**

| Step                   | Duration        |
| ---------------------- | --------------- |
| Diff-based SFTP deploy | ~30 sec - 2 min |
| WP-CLI + cache clear   | ~30 sec         |
| npm ci                 | ~25 sec         |
| Cypress tests          | ~2 min          |
| No cleanup             | —               |
| **Total**              | **~4-5 min**    |

**Caveats:**

- More complex script, need to handle directory creation for new files
- Need to handle binary files (images) carefully
- First run still needs full mirror if theme directory doesn't exist
- Edge case: if staging was manually modified, diff won't capture that

#### 2c. rsync over SSH (saves ~8-10 min, simpler than 2b)

rsync does efficient delta transfers automatically. Only transfers bytes that changed.

```yaml
- name: Deploy to staging via rsync
  run: |
    rsync -avz --delete \
      --exclude '.git/' \
      --exclude '.github/' \
      --exclude 'node_modules/' \
      --exclude 'cypress/videos/' \
      --exclude 'cypress/screenshots/' \
      -e "ssh -i ~/.ssh/kinsta_key -p ${{ secrets.KINSTA_SSH_PORT }}" \
      ./ ${{ secrets.KINSTA_SSH_USER }}@${{ secrets.KINSTA_SFTP_HOST }}:public/wp-content/themes/novaramedia-com/
```

**Prerequisite:** rsync available on the Kinsta server (likely, since most Linux servers have it).

**Projected total: ~5-6 min** (rsync is fast for incremental updates but still needs to checksum all files on first comparison)

---

## Recommended Approach

```
1. Check if git is available on Kinsta staging SSH
   → If YES: implement Tier 1 (git pull). Target: ~4 min.
   → If NO: check if rsync is available
     → If YES: implement Tier 2c (rsync). Target: ~5-6 min.
     → If NO: implement Tier 2a + 2b (remove cleanup + diff SFTP). Target: ~4-5 min.
```

## Quick Verification Commands

Run these to check server capabilities:

```bash
# Check for git
ssh -i ~/.ssh/kinsta_key -p $KINSTA_SSH_PORT $KINSTA_SSH_USER@$KINSTA_SFTP_HOST "which git && git --version"

# Check for rsync
ssh -i ~/.ssh/kinsta_key -p $KINSTA_SSH_PORT $KINSTA_SSH_USER@$KINSTA_SFTP_HOST "which rsync && rsync --version | head -1"
```

## Other Minor Optimisations

These are small wins regardless of deploy strategy:

- **npm ci caching:** Already using `setup-node` with `cache: 'npm'` — good
- **Cypress binary caching:** Could cache `~/.cache/Cypress` between runs (saves ~10 sec)
- **Parallel test specs:** Cypress Cloud or `cypress-split` to run specs in parallel across containers (overkill for 2 min of tests)
- **Remove temporary push trigger:** The `push: branches: [copilot/add-cypress-testing-ci]` trigger should be removed after merging to development
