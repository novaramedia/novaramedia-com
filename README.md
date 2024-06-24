# no future utopia now

novaramedia.com

## We are Dev[0]

### Howto: release

- Pull `development`
- `yarn release`
- Don't commit, tag or push in release-it process
- After post release-it scripts are run commit in format `Build: x.x.x`
- Create PR to master branch in format `Version x.x.x` with changelog entries as description

### Semver

- Patches for bugfixes, copy updates, minor changes
- Minor version for any significant new functionality
- Major for breaking changes and significant design system iterations
