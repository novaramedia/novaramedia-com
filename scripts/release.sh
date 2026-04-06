#!/usr/bin/env bash
#
# Automated release script for novaramedia-com theme.
# Runs the full release flow non-interactively:
#   1. Validates branch state
#   2. Runs release-it (bumps version, updates changelog, builds, updates style.css, creates tag)
#   3. Commits all changes as "Build: x.x.x"
#   4. Pushes development branch and tag
#   5. Creates GitHub Release (triggers production deploy)
#
# Usage:
#   ./scripts/release.sh [increment]
#
#   increment: major | minor | patch (default: minor)
#
# Examples:
#   ./scripts/release.sh           # minor bump
#   ./scripts/release.sh patch     # patch bump

set -euo pipefail

INCREMENT="minor"

for arg in "$@"; do
  case "$arg" in
    major|minor|patch) INCREMENT="$arg" ;;
    *) echo "Unknown argument: $arg"; exit 1 ;;
  esac
done
BRANCH=$(git branch --show-current)

# --- Preflight checks ---

if [ "$BRANCH" != "development" ]; then
  echo "Error: Must be on 'development' branch (currently on '$BRANCH')"
  exit 1
fi

if ! git diff --quiet || ! git diff --cached --quiet; then
  echo "Error: Working tree has uncommitted changes. Commit or stash them first."
  exit 1
fi

echo "Pulling latest development..."
git pull --ff-only origin development

# --- Run release-it ---

echo ""
echo "Running release-it --ci --increment=$INCREMENT ..."
echo ""
npx release-it "$INCREMENT" --ci

# --- Read new version ---

VERSION=$(node -p "require('./package.json').version")
TAG="v$VERSION"
echo ""
echo "Version bumped to $VERSION"

# --- Commit ---

echo ""
echo "Committing all changes as 'Build: $VERSION' ..."
git add -A
git commit -m "Build: $VERSION"

echo ""
echo "Pushing development and tag..."
git push origin development
git push origin "$TAG"

echo ""
echo "Release $VERSION complete."
echo "GitHub Release created → production deploy will trigger automatically."
