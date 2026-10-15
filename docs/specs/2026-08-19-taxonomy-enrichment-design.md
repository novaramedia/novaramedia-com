# Taxonomy Enrichment System — Design Spec

**Date:** 2026-08-19
**Status:** Approved design, pre-implementation
**Scope:** Three deliverables — public taxonomy restructure, `nm-taxonomy` companion plugin, external LLM classification agent. The theme is a consumer, not the home, of this system.

## 1. Problem

Novara Media's thematic categorisation is inconsistent and under-used, blocking two goals:

1. **Front-end thematic navigation** — visual design that lets readers navigate themes. Not buildable on current data quality.
2. **Internal coverage analytics** — week-on-week understanding of what subjects are and aren't being covered.

### Audit findings (2026-08-18, local DB snapshot + live site)

Four parallel taxonomy systems, each doing a different job badly or partially:

| Taxonomy | State |
|---|---|
| `category` | Formats/shows. 100% coverage, 47 terms. Cruft: 3× "Interview", 2× "Downstream", 2× "Bastani Factor", topical strays (GE2019, US Election 2020), 28 posts Uncategorised. |
| `section` | Themes + geography merged in one hierarchy. 44 terms, 5 top-level (Society, Politics, UK, Culture, World). Introduced ~2021-22, **never backfilled** — zero coverage 2011-2020 (~2,700 posts), ~46% peak (2022), ongoing but partial on live. Registered in the theme (`lib/taxonomies.php`), no `show_in_rest`. |
| `focus` | 16 dormant campaign/series terms, mostly 2017-19 era. |
| `post_tag` | 6,937 terms, 29,882 assignments. 59% singletons/orphans. Duplicates (covid/covid-19/coronavirus). Untyped mix of people, shows, topics. |

Live site: 6,905 published posts (local snapshot stale at 4,324 — fresh DB pull required before migration work). Live site runs WP 6.9+ (core Abilities API REST namespace present, auth-gated).

Root cause: editors are rushed and apply inconsistent logics; nobody's job is taxonomy consistency. Conclusion: consistency work belongs to a machine agent; **curation and public IA decisions stay human**.

## 2. Decisions (locked)

1. **Two-layer model.** Public taxonomies stay small and human-curated. A dense analytic layer lives in *curated tags* (no third hidden taxonomy). The analytic layer is the evidence base for promoting topics to public sections ("AI has 87 posts and no public home → promote" — deliberate editorial act, informed by data).
2. **Facet split.** `section` becomes purely thematic (Society, Politics, Culture + children — existing URLs unchanged). New `region` taxonomy takes the UK and World subtrees. A post can carry both facets; programmatic views can intersect them (e.g. Conflict × not-Americas).
3. **Agent may assign but never create `section`/`region` terms.** Enforced in ability input schema server-side, not just prompts. Agent may propose new *curated tags* (queued).
4. **Staged trust model.** Stage 0 suggest-only → Stage 1 auto-apply above confidence threshold → Stage 2 autonomous with spot audits. Promotion between stages gated on ledger metrics (approval rate, revert rate), not vibes. The deferred question "is the agent good enough to share the tags namespace?" is answered by these metrics and remains reversible via the ledger.
5. **Hybrid LLM deployment (option C).** Hosted small model (Gemini Flash / Claude Haiku — eval decides) for backfill + quality baseline; local model on Glados eligible later behind the same provider interface, promoted only if eval scores hold.
6. **Companion plugin, not theme integration.** Decisive reason: deploy cadence — agent-facing endpoints and suggestion schema will iterate faster than themed releases. Also: content model must survive theme redesign (which is this project's endgame); theme build system is frozen; isolates the security-reviewable surface (DB table + authenticated write endpoints).
7. **WordPress Abilities API over bespoke REST.** Core-stable since WP 6.9. Abilities give JSON-schema'd contracts, per-ability permission callbacks, and the free core execution layer `POST /wp-json/wp-abilities/v1/abilities/{name}/run`. Zero `register_rest_route()` code. **MCP Adapter explicitly deferred** — 0.x, GitHub-only, recent breaking changes; our agent is a single known headless client. Adding MCP later = `meta.mcp.public => true`.

## 3. Architecture

```
┌─────────────────────────┐          ┌──────────────────────────────┐
│  nm-taxonomy-agent      │  HTTPS   │  WordPress (Kinsta)          │
│  (new repo, Glados)     │─────────▶│  nm-taxonomy plugin          │
│  - backfill / watch /   │ abilities│  - taxonomies (section,      │
│    report modes         │  REST +  │    region, term meta)        │
│  - LLM provider iface   │ app pass │  - suggestion ledger table   │
│  - eval harness         │          │  - 4 abilities               │
│  - Sentry (errors+crons)│          │  - review queue (wp-admin)   │
└─────────────────────────┘          │  - coverage reports          │
                                     │  - Sentry (plugin scope)     │
                                     └──────────────────────────────┘
                                        theme = consumer (templates,
                                        nav rendering) only
```

### 3.1 Public taxonomy restructure

- `section` keeps name/slug/URLs; becomes thematic-only.
- New `region` taxonomy (hierarchical, public): UK and World subtrees move over. Migration = updating the `taxonomy` column on ~20 `term_taxonomy` rows; post↔term assignments survive untouched. Shipped as an idempotent wp-cli command in the plugin.
- Redirects `/section/uk/*` → `/region/uk/*` etc. via `template_redirect` in the plugin.
- Both registered in the plugin with `show_in_rest => true`. Term creation locked to humans (capability).
- Theme changes: `taxonomy-region.php` template; `lib/taxonomies.php` guarded with `taxonomy_exists()` during transition, deleted once plugin proven.
- **Not in scope:** term pruning (backfill first, prune from evidence after); `focus` (dormant, revisit after curated tags exist — likely absorbed there); `category` cleanup (separate small ticket).
- Soft rule post-restructure: every post should carry ≥1 section; region only when geographically specific. Enforced by agent suggestions + coverage reports, not hard validation.

### 3.2 Curated tags layer

- Single shared `post_tag` namespace. Curated core marked by term meta: `nm_curated = 1`, `nm_type = person | org | place | topic | show` (exactly five types).
- Type is invisible metadata (termmeta), never part of display names. Purpose: (a) honest analytics — subject reports count `topic` tags only; (b) promotion gating — only `topic` tags are section candidates; (c) agent discipline — every proposed tag must declare a type. Optional small type dropdown on the tag-edit screen (nice-to-have).
- Analytics/reports read curated tags only. The ~3,988 singletons stay untouched and excluded — non-destructive default.
- Synonym merges (covid/covid-19/coronavirus) record `nm_aliases` term meta on the survivor. Merges are always editor-approved regardless of trust stage.
- Agent-created terms stamped `nm_created_by = agent` in term meta.

### 3.3 Suggestion ledger (custom table `nm_tax_suggestions`)

Doubles as queue and permanent audit log. Row shape:

```
id, post_id, taxonomy, action (assign|create_assign|merge|unassign),
term_id?, proposed_name?, proposed_type?, confidence, model, agent_version,
status (pending|approved|rejected|auto_applied|reverted),
created_at, decided_by?, decided_at?
```

- Every agent write flows through a row, including auto-applied ones in later stages.
- Bulk revert = replay ledger backwards filtered by model/version/date (wp-cli `revert` command).
- Human edits via normal WP admin bypass the ledger (already attributed by WP).
- Suggestion schema mirrors the canonical WordPress AI plugin's `{term, confidence, is_new, parent}` shape so future core UI convergence stays cheap.

### 3.4 Abilities (registered by plugin, `novara/` namespace)

| Ability | Purpose | Permission |
|---|---|---|
| `novara/list-posts-needing-classification` | Cursor-paged posts + content + current terms + vocab context | `edit_posts` |
| `novara/suggest-terms` | Push suggestion rows; validates against schema; rejects section/region `create_assign`; dedupes vs pending + already-assigned | `edit_posts` |
| `novara/apply-approved-terms` | Apply approved rows (also the internal code path for queue UI approvals) | `manage_categories` |
| `novara/get-coverage-report` | Aggregated coverage + promotion-candidate data | `edit_posts` |

Auth: WordPress application password for a dedicated agent user. Agent sends a distinctive User-Agent (`nm-taxonomy-agent/x.y`). Kinsta Bot Protection allowlist verified before first sustained run.

### 3.5 Agent service (new repo, TypeScript/Node, runs on Glados)

Modes (systemd timer / cron):

- **`backfill`** — batches over all ~6,905 posts, cursor-checkpointed, resumable, paced over days.
- **`watch`** — daily run over posts published/updated since last cursor.
- **`report`** — weekly pull of `get-coverage-report` → formatted digest → Slack.

Per-post pipeline: fetch → assemble context (title, trimmed body, excerpt, existing terms, category/show) → single LLM call with strict JSON schema output `{section: assign[], region: assign[], tags: (assign|propose{name,type})[], confidence each}` → local validation (reject section/region creations, dedupe, threshold) → push via `suggest-terms`.

LLM provider interface: `classify(post, vocab) → suggestions`. Hosted provider first; Ollama/OpenAI-compatible local provider later. Swap = config.

Confidence calibration starting point (from canonical AI plugin's measured data): minimum 0.6; true positives cluster ≥0.85. Auto-apply threshold (Stage 1) starts at 0.85, tuned from ledger data.

### 3.6 Review queue + reports (wp-admin)

- "Taxonomy Suggestions" admin page: `WP_List_Table` + minimal vanilla JS, zero build step.
- Grouped-by-post default view (title, current terms, suggested additions with confidence chips). Filters: taxonomy, confidence band, action, date. Batch actions: approve selected, approve-all-≥0.85, reject selected.
- Separate tabs: new curated tag proposals (name + type check), merges (admin-only).
- Capability: `manage_categories` for queue actions; merges admin-only.
- Reports: weekly (coverage %, topic movement, **overflowing buckets** = high-volume topic tags with no public section, agent health) and monthly (+ trends, dead public sections as prune candidates). Rendered as admin page; same dataset posted to Slack weekly by agent report mode.

### 3.7 Monitoring (Sentry)

- **Agent:** `@sentry/node` — error capture, release tagging by `agent_version`, **Sentry Crons check-ins** on all three scheduled modes (missed-run detection covers silent stalls). Alerts via existing Sentry→Slack.
- **Plugin:** PHP capture scoped to plugin code paths (ability execution, migration command, queue actions). `wp-sentry` site-wide vs scoped `sentry/sentry` init — implementation-plan decision.

## 4. Eval harness — "better than the average editor" gate

Golden set: ~300 well-sectioned posts from the 2022 peak-coverage era. Score classifier precision/recall vs human assignments per taxonomy. Benchmark against canonical `ai/content-classification` ability as baseline. The eval decides: hosted model choice → thresholds → later, Glados-local promotion. Ledger metrics (approval %, revert %) then govern trust-stage promotion in production.

## 5. Rollout — independently shippable steps

1. **Plugin v0:** taxonomy registration relocation, section/region migration command, redirects, term meta plumbing, `show_in_rest`.
2. **Plugin v1:** ledger table, four abilities, review queue, report page.
3. **Agent v0 + eval:** pipeline, golden set, model choice, calibration.
4. **Stage 0 live:** watch mode, suggest-only, editors work the queue, measure approval rate.
5. **Backfill** through the same queue (auto-apply ≥0.85 only if Stage 1 already earned).
6. **Reports** to Slack weekly.
7. Trust-stage promotions + Glados-local trial, gated on metrics.

## 6. Testing

- **Plugin:** PHPUnit via wp-env — ability permission callbacks, schema validation, apply logic, migration idempotency (run twice = no-op). Migration rehearsed on a fresh live DB copy first.
- **Agent:** unit tests on validation/dedupe/threshold logic; prompt snapshot tests; eval harness as the quality gate.

## 7. Error handling

- Agent: per-post failure isolation, batch cursor never advances past unprocessed posts, exponential backoff, dead-letter after N retries. Auth failure / bot-block → Sentry alert (→ Slack), never silent.
- `suggest-terms` idempotent: re-running a batch never double-queues.
- Ledger revert via wp-cli by model/version/date.

## 8. Open items (discovery during implementation planning)

- Glados hardware spec (governs local-model viability).
- Slack channel for reports/alerts.
- Hosted model choice (eval decides).
- Fresh Kinsta DB pull for local dev + migration rehearsal.
- `wp-sentry` site-wide vs plugin-scoped Sentry init.
- Agent repo name + hosting conventions (follow `nm-agents-shared/conventions.md`).
