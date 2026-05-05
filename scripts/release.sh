#!/usr/bin/env bash
#
# Automated release script for novaramedia-com theme.
# Runs the full release flow non-interactively:
#   1. Validates branch state
#   2. Runs release-it (bumps version, updates changelog, builds, updates style.css)
#   3. Commits all changes as "Build: x.x.x"
#   4. Creates git tag and pushes development branch + tag
#   5. Creates GitHub Release via gh CLI (triggers production deploy)
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

# --- Tag and release ---

if git rev-parse "$TAG" >/dev/null 2>&1; then
  echo "Error: Tag $TAG already exists locally."
  echo "If this is from a previous failed release, delete it with:"
  echo "  git tag -d $TAG"
  echo "  git push origin :refs/tags/$TAG   # only if it was already pushed"
  exit 1
fi

if git ls-remote --exit-code --tags origin "$TAG" >/dev/null 2>&1; then
  echo "Error: Tag $TAG already exists on remote origin."
  echo "If this is from a previous failed release, delete it with:"
  echo "  git push origin :refs/tags/$TAG"
  exit 1
fi

echo ""
echo "Creating tag $TAG..."
git tag "$TAG"

echo ""
echo "Pushing development and tag..."
git push origin development
git push origin "$TAG"

# --- Create GitHub Release (triggers production deploy) ---

echo ""
echo "Creating GitHub Release..."
NOTES_FILE=$(mktemp)
trap 'rm -f "$NOTES_FILE"' EXIT
sed -n "/^## \[$VERSION\]/,/^## \[/p" CHANGELOG.md | sed '$d' | tail -n +2 > "$NOTES_FILE"

gh release create "$TAG" \
  --title "$TAG" \
  --notes-file "$NOTES_FILE" \
  --target development

echo ""
echo "Release $VERSION complete."
echo "GitHub Release created → production deploy will trigger automatically."
