# Dependency overrides

`package.json` `overrides` force patched versions of transitive dev dependencies
whose parents still ask for a vulnerable range. JSON allows no comments, so each
override is recorded here with why it exists and when it can go.

Review this list on every dependency update batch. An override that outlives its
reason hides the real dependency tree and can block legitimate upgrades.

## Current overrides

| Override                    | Forces    | Pulled in by                                                                                                                                       | Remove when                                                                                       |
| --------------------------- | --------- | -------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------- |
| `uuid`                      | `^11.1.1` | `webpack-notifier` → `node-notifier@9` (`^8.3.0`)                                                                                                  | `node-notifier` asks for `uuid` ≥11.1.1                                                           |
| `postcss-selector-parser@6` | `^7.1.6`  | `@wordpress/scripts` → `cssnano@6` → `postcss-calc@9`, `postcss-merge-rules`, `postcss-minify-selectors`, `postcss-unique-selectors`, `stylehacks` | `@wordpress/scripts` moves to cssnano 7+                                                          |
| `serialize-javascript`      | `^7.1.3`  | `@wordpress/scripts` → `copy-webpack-plugin@10` (`^6.0.0`)                                                                                         | `@wordpress/scripts` moves to a `copy-webpack-plugin` that asks for `serialize-javascript` ≥7.1.3 |
| `proxy-agent`               | `^8.0.3`  | `release-it` pins `proxy-agent` 8.0.2 → `pac-proxy-agent` 9.1.0 → `get-uri` 8.0.1 (all exact), which asks for `basic-ftp` `^5.3.1`                 | `release-it` asks for `proxy-agent` ≥8.0.3                                                        |
| `glob@10`                   | `^14.0.2` | `stylus@0.64` (`^10.4.5`); glob 10 also pulls `minimatch@9`                                                                                        | `stylus` asks for `glob` ≥14.0.2. Stylus only calls `glob.sync`, which glob 14 keeps              |
| `minimatch@10`              | `^11.0.1` | `markdownlint-cli` (`~10.2.6`)                                                                                                                     | `markdownlint-cli` asks for `minimatch` ≥11.0.1                                                   |
| `markdownlint-cli`          | `^0.50.0` | `@wordpress/scripts@37` (`^0.49.1`), whose 0.49 pins vulnerable `js-yaml` and `smol-toml`                                                          | `@wordpress/scripts` asks for `markdownlint-cli` ≥0.50                                            |

Several rows depend on `@wordpress/scripts`, so check them whenever it gets a major bump.

Known and accepted: `katex` <0.18.2 (low) via `markdownlint` → `micromark-extension-math` (`^0.16.0`). It only renders math in Markdown linting, which the theme does not run, and the fix is a breaking `katex` 0.x bump. Drop this note when `micromark-extension-math` asks for `katex` ≥0.18.2.

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
