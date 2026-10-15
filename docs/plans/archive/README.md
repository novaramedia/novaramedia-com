# Archived plans

Plans whose work has shipped. Moved here rather than deleted so the design
reasoning stays greppable.

Convention: when a plan's feature is released, `git mv` the plan into this
folder in the same PR that wraps up the work (or the next docs pass), and note
the release version below.

| Plan | Shipped in |
|------|------------|
| `ci-speedup.md` | 4.7.0 — git checkout deploy replaced SFTP in `cypress.yml` |
| `front-page-layout-editor.md` | 4.7.0 — Front Page > Layout sortable list |
| `cypress-to-playwright.md` | 4.9.0 (#600) — Playwright replaced Cypress at 1:1 coverage; Phase 3 backlog lives in `docs/testing/testing.md` |
| `cortado-category-archive.md` | 4.9.0 (#608) — The Cortado category archive, front-page block, inline signup |
| `cortado-category-archive-implementation.md` | 4.9.0 (#608) — build plan for the above |
