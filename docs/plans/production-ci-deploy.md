# Production CI Deploy

**Status:** planned, not started.

## Why

Production is deployed by a manual WP Pusher update after the release PR merges
(see [`docs/releases.md`](../releases.md#deploying)). That leaves a gap between
"merged" and "live" that nothing tracks, and it means production and staging are
deployed by two different mechanisms. Staging already deploys from GitHub Actions
over SSH + git; production should work the same way.

## Target

1. **Deploy workflow for production.** A workflow modelled on
   `deploy-staging.yml` — SSH to the Kinsta production environment, fetch, and
   hard-reset the theme checkout to `origin/master` — triggered on push to
   `master` (i.e. when a release PR merges). Own concurrency group, separate from
   `kinsta-staging`. Clear the Kinsta cache for the production environment
   afterwards, as the staging workflow does.
2. **Release notification follows the deploy.** `release-notification.yml`
   currently fires on the release PR merge and posts to the private digital team
   channel, which is right while it only reports a merge. Once the deploy runs
   from CI, the notification should run after a successful production deploy
   and post to the **public** digital team channel. The channel is set by the
   incoming webhook, so this needs a new webhook for the public channel stored
   as a repository secret, not a code change to a channel ID.
3. **Retire WP Pusher on production** once the workflow has shipped a release
   cleanly. Remove the plugin and any GitHub webhook pointing at it.

## Open questions

- Production SSH credentials and environment ID as repository secrets, or scoped
  to a GitHub `production` environment with required reviewers.
- Whether the deploy should wait on the Playwright run for the release PR.
- Rollback path: redeploy a previous tag/SHA via `workflow_dispatch` on the same
  workflow.
- The first run needs the production theme directory to be a git checkout (the
  staging workflow re-clones if `.git` is missing — confirm what WP Pusher leaves
  behind).

## Housekeeping found while documenting (2026-09-28)

- The repository still has an active push webhook for WP Pusher on the staging
  site, although staging no longer uses WP Pusher. Remove it, and the plugin if it
  is still installed on staging.

Build-system/CI changes need team approval before implementation.
