# CLAUDE.md

This file provides guidance to Claude Code when working in this repository.

## Project Context

**Type:** Professional (Novara Media)
**Notion Access:** READ-ONLY (Novara workspace - separate from personal integration)

## Project References

### Notion (Novara Media Workspace)

- **Project Record:** [novaramedia-com v4.4.0](https://www.notion.so/novaramedia/novaramedia-com-v4-4-0-4982b2ecfcb64fd8b2583b409a7a0276)
  - *Note: This is in the Novara Media Notion workspace. Access manually via browser.*

### GitHub

- **Repository:** [novaramedia/novaramedia-com](https://github.com/novaramedia/novaramedia-com)
- **Primary Branch:** development
- **PR Target:** development (then merge to master for releases)

## Restrictions

- **NO Notion writes** - Notion is READ-ONLY in professional context
- **Production-quality code only** - No experimental outputs
- **Follow existing patterns** - Check codebase conventions first
- **Don't modify build system** - Webpack/release config changes require team approval

## Code Standards

See [`.github/copilot-instructions.md`](.github/copilot-instructions.md) for detailed coding standards including:

- JavaScript module patterns and structure
- Stylus/CSS design system (nm-stylus-library)
- WordPress template patterns
- Build commands and release process
- Git workflow and commit guidelines

**Key points:**
- Modern WordPress theme with modular JavaScript (Webpack)
- 24-column grid system with responsive breakpoints
- BEM-like CSS naming conventions
- Always run `npm run build` to verify changes
- Only commit `dist/` when source files actually changed

## Working With Other Agents

This codebase is worked on by:
- Claude Code (terminal-based AI)
- GitHub Copilot (editor-based AI)
- Human developers

### Conventions for Multi-Agent Work

- Commit messages should be clear and self-explanatory
- Avoid partial implementations that require context to understand
- Document non-obvious decisions in code comments or PR descriptions
- Check copilot-instructions.md for shared standards

---

*This file is compatible with GitHub Copilot instructions format.*
