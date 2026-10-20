# Dependency overrides

`package.json` `overrides` force patched versions of transitive dev dependencies
whose parents still ask for a vulnerable range. JSON allows no comments, so each
override is recorded here with why it exists and when it can go.

Review this list on every dependency update batch. An override that outlives its
reason hides the real dependency tree and can block legitimate upgrades.

## Current overrides

| Override                    | Forces    | Pulled in by                                                                                                                                       | Remove when                                                                                            |
| --------------------------- | --------- | -------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------ |
| `markdown-it`               | `^14.1.2` | `@wordpress/scripts` → `markdownlint-cli@0.31` → `markdownlint@0.25`, which pins `markdown-it` 12.3.2                                              | `@wordpress/scripts` moves to a `markdownlint-cli` whose `markdownlint` asks for `markdown-it` ≥14.1.2 |
| `uuid`                      | `^11.1.1` | `@wordpress/scripts` → `webpack-dev-server` → `sockjs` (`^8.3.2`); `webpack-notifier` → `node-notifier` (`^8.3.0`)                                 | Neither `sockjs` nor `node-notifier` asks for `uuid` 8                                                 |
| `minimatch@3`               | `^3.1.5`  | `@wordpress/scripts` → `markdownlint-cli@0.31`, which pins `minimatch` `~3.0.5`                                                                    | `@wordpress/scripts` moves to a `markdownlint-cli` that drops the `~3.0` pin                           |
| `postcss-selector-parser@6` | `^7.1.6`  | `@wordpress/scripts` → `cssnano@6` → `postcss-calc@9`, `postcss-merge-rules`, `postcss-minify-selectors`, `postcss-unique-selectors`, `stylehacks` | `@wordpress/scripts` moves to cssnano 7+                                                               |
| `serialize-javascript`      | `^7.1.0`  | `@wordpress/scripts` → `copy-webpack-plugin@10` (`^6.0.0`)                                                                                         | `@wordpress/scripts` moves to a `copy-webpack-plugin` that asks for `serialize-javascript` 7           |

Most rows depend on `@wordpress/scripts`, so check them whenever it gets a major bump.

## Checking a row

Quick look at what a parent currently asks for:

```bash
npm view @wordpress/scripts dependencies.cssnano
npm view @wordpress/scripts dependencies.markdownlint-cli
npm view @wordpress/scripts dependencies.copy-webpack-plugin
```

To confirm an override can go:

1. Remove its line from `overrides` and run `npm install`
2. `npm ls <package> --all`: no version in the vulnerable range comes back
3. `npm audit`: high/critical counts unchanged
4. `npm run build`: `dist/` and `blocks/` output unchanged

If all four hold, commit the removal and delete the row here. If a vulnerable
version comes back, restore the line and update the row's "Remove when" if the
reason has changed.

## Adding an override

Prefer bumping the parent. Override only when no parent release fixes the
chain. Scope it to the vulnerable major (`pkg@6`) where the tree also holds a
newer major, add a row here in the same commit, and A/B the build output.
