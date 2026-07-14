---
name: enforce-phpcs-lint
enabled: true
event: file
conditions:
  - field: file_path
    operator: regex_match
    pattern: \.php$
action: warn
---

**Run PHPCS on this file after editing.**

After editing a PHP file, run `phpcs --standard=phpcs.xml <file_path>` to check for linting errors. If errors are found, fix them before moving on. This ensures all PHP edits conform to the project's coding standards, just as they would in an editor.
