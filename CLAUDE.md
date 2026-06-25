# CLAUDE.md

WordPress theme for novaramedia.com. PHP + modular JS (Webpack) + Stylus (nm-stylus-library).

## Non-negotiable rules

- **Production-quality code only** — no experimental outputs
- **Don't modify build system** — Webpack/release config requires team approval
- **`dist/` commits** — only when source files actually changed (`npm run build` to verify)

## References

- GitHub: [novaramedia/novaramedia-com](https://github.com/novaramedia/novaramedia-com) — branch: `development`, PR target: `development`
- Notion project: search Novara workspace for `novaramedia-com` to find current version record (new record created each minor release)
- Code standards: `.github/copilot-instructions.md`
- Cross-project conventions: `nm-agents-shared/conventions.md`

## Docs

- `docs/architecture/` — block rendering, oEmbed privacy
- `docs/plans/` — embed consent gate, multi-newsletter signup, CI speedup
- `docs/specs/` — latest articles news category
- `docs/testing/` — Cypress testing, workflow notes, testing overview
- `docs/security.md` — security notes
- `docs/post-deploy-checklist.md` — manual steps after each release (rewrite flushes, one-time admin saves, cache/CDN verification)
- `docs/extended-changelogs/` — verbose PR changelogs
