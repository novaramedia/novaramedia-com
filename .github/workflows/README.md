# GitHub Actions Workflows

## Release Notification to Slack

The `release-notification.yml` workflow automatically sends structured notifications to the public digital team Slack channel when a new version is released.

### How it works

1. **Trigger**: Activates when a Pull Request with title starting with "Version " is merged into the `master` or `main` branch
2. **Version Extraction**: Extracts the version number from the PR title (e.g., "Version 4.2.10" → "4.2.10")  
3. **Release Notes**: Parses `CHANGELOG.md` to extract the release notes for that specific version
4. **Slack Notification**: Sends a structured message with:
   - Release version and repository info
   - Link to the merged PR
   - Complete release notes from changelog
   - Commit SHA for deployment tracking

### Setup Requirements

To enable Slack notifications, you need to:

1. **Create a Slack Webhook URL**:
   - Go to your Slack workspace settings
   - Navigate to "Incoming Webhooks" 
   - Create a new webhook for the target channel
   - Copy the webhook URL

2. **Add the webhook as a GitHub secret**:
   - Go to repository Settings → Secrets and variables → Actions
   - Add a new repository secret named `SLACK_WEBHOOK_URL`
   - Paste the webhook URL as the value

### Message Format

The Slack notification includes:
- Header with release version
- Repository and PR links
- Full release notes from CHANGELOG.md
- Deployment commit information

### Troubleshooting

- Ensure PR titles follow the exact format: "Version X.Y.Z"
- Verify the version exists in CHANGELOG.md with format: `## [X.Y.Z] - DATE`
- Check that the `SLACK_WEBHOOK_URL` secret is properly configured