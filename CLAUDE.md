# Plugin: BuddyPress Job Manager

## Direction — read before proposing anything

**This is a bridge, not a job board.** It renders WP Job Manager and its add-ons inside
BuddyPress profiles. It owns no tables, no post types, and stores nothing but its settings.

**Why it exists:** the profile is already the member's professional identity. You open
someone's profile to find out who they are — if they are job-hunting their resume belongs
there, if they are hiring their jobs belong there. The community's social graph becomes the
hiring graph, and nobody leaves their profile to manage either side.

**The whole product is two journeys.** Hiring: post a job → see it on your profile → be told
who applied. Looking for work: add a resume → it shows on your profile → apply from a job
listing → see where each application stands. *If a screen, setting, or stored value does not
sit on one of those two lines, it is junk.*

Three rules that follow, and that most of this plugin's historical bugs came from breaking:

1. **Never rebuild what the host plugin already renders.** Use its shortcode. A re-render
   drifts from the original, and it duplicates strings into our text domain that the host
   already has community translations for — so a translated site shows our tab in English.
2. **Never store what can be derived at render time.** Per-user state written at activation
   only ever covers users who existed then; everyone who registers later silently misses it.
3. **Never add a permission system beside WP Job Manager's.** Defer to its capability.

The plugin drifted from this over time, and that drift is the reason maintenance on a free
plugin has cost as much as it has. Current plan, with locked wording per screen:
[Job Manager Screen Spec](https://claude.ai/code/artifact/bf9da7b4-c305-4cc9-81d1-49abad7cc0c7).
**Work in progress — read [`audit/HANDOFF-3.0.0.md`](audit/HANDOFF-3.0.0.md) before starting.**

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory — 0 REST endpoints, 1 AJAX action (`bpjm_load_more_jobs`), 2 admin pages (the shared `wbcomplugins` hub + this plugin's single 4-tab page), 1 settings option (`bpjm_general_settings`), 0 shortcodes, 0 blocks, 0 tables, 0 cron, 0 CPTs. Use it before grepping. See also [`audit/FEATURE_AUDIT.md`](audit/FEATURE_AUDIT.md), [`audit/CODE_FLOWS.md`](audit/CODE_FLOWS.md), [`audit/ROLE_MATRIX.md`](audit/ROLE_MATRIX.md), and the wppqa baseline at [`audit/wppqa-baseline-2026-06-05/SUMMARY.md`](audit/wppqa-baseline-2026-06-05/SUMMARY.md). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.

> **Customer + developer docs:** [`docs/website/`](docs/website/) - getting-started,
> settings, developer-guide. GitHub-only; never published, synced or uploaded.
> The developer guide carries the verified hook list and the three architecture rules.

> **Development conventions:** follow the [`wp-plugin-development`](https://example/skills/wp-plugin-development) skill for ALL code work (architecture, hooks ownership, REST patterns, escaping, security, admin UI, release packaging). This plugin predates the modern conventions; align new code to the skill, do not copy the legacy patterns below.

## Quick reference
- **Main file**: `bp-job-manager.php`
- **Version**: `3.0.0` (branch `3.0.0`)
- **Namespace**: none — class-prefix convention `Bp_Job_Manager_*`
- **Text domain**: `bp-job-manager`
- **Extends**: null (standalone; not a Free/Pro pair)
- **Requires (hard)**: BuddyPress + WP Job Manager ONLY (gate in `bpjm_plugin_init()`, `bp-job-manager.php`; header `Requires Plugins: buddypress, wp-job-manager`). Resume Manager, Applications, Bookmarks and Alerts are **optional** add-ons, each detected at runtime via `bpjm_is_plugin_active()` and each unlocking one nav/notification surface.
- **Requires at least**: WP `6.5` (added to the header in 2.8.5; readme floor is now 6.5, was 3.0.1) — **Requires PHP**: `8.0`
- **The dependency gate is user-independent.** It must never be ANDed with a capability check. `Requires Plugins` only blocks *activation* (core checks it in `validate_plugin_requirements()`); it cannot stop an already-active plugin whose dependency was deleted from disk. When that happens the gate is the only thing standing between a visitor and a fatal, because ~120 `bp_*()` call sites assume BuddyPress is loaded.

## Admin wrapper classification: **NEW** (card-panel)
Migration DONE in 2.8.5. `includes/admin/views/shell.php` EXISTS; the legacy `admin/wbcom/` wrapper (class `Wbcom_Admin_Settings`) was **deleted**.
- Admin UI owner: `includes/admin/class-bp-job-manager-admin-panel.php` (`Bp_Job_Manager_Admin_Panel`, wired at `includes/class-bp-job-manager.php:205-206`).
- Admin menu: `admin_menu` -> `Bp_Job_Manager_Admin_Panel::add_menu` (parent slug `wbcomplugins`, own page slug `bp-job-manager`). Plus `admin_menu@999` -> `takeover_hub_landing` so this plugin renders the shared hub landing (`includes/admin/views/hub.php`) even when an un-migrated Wbcom plugin registered the menu first.
- **ONE page, 4 tabs** (`get_tabs()` @ `:49-74`, filterable via `bpjm_admin_tabs`): Overview, General, Discover, Help & Support. The old Our Plugins / Our Themes / Support wrapper pages, the `wbcom_admin_setting_header` shortcode and the `wbcom_addons_cards` AJAX action are all GONE.
- `Bp_Job_Manager_Admin` (`admin/class-bp-job-manager-admin.php`) is retained for **non-UI helpers only**: admin-bar links, activity writers + cleanup, settings sanitizer. Its UI methods are no longer hooked.

## Key entry points
- Bootstrap: `bp-job-manager.php` (dependency gate, activation redirect)
- Orchestrator: `includes/class-bp-job-manager.php` (wires all hooks via `Bp_Job_Manager_Loader`)
- Runtime config: `includes/class-bp-job-manager-globals.php` (`global $bpjm_bp_job_manager` from option `bpjm_general_settings`)
- Admin UI (menu, tabs, settings registration, enqueue): `includes/admin/class-bp-job-manager-admin-panel.php`
- Admin non-UI helpers (admin bar, activity writers, sanitizer): `admin/class-bp-job-manager-admin.php`
- Public (everything user-facing): `public/class-bp-job-manager-public.php`
- Settings form: `includes/admin/views/settings-general.php` (rendered through `includes/admin/views/shell.php`)

## Architecture
WordPress-Plugin-Boilerplate layout (Bootstrap -> Loader -> Globals -> Admin/Public). No PHP namespace, no service container, no DB tables, no REST. Integration is hook-driven: it consumes BuddyPress + WP Job Manager / Resume Manager / Applications hooks and surfaces job/resume management inside BP member profiles. See `audit/CODE_FLOWS.md` for the six runtime flows.

## Important patterns
- **No own tables / CPTs** — operates entirely on WPJM `job_listing` / `resume` posts and BP `bp_activity`.
- **No role gating.** A tab appears because the member has jobs or resumes, or because it is their own profile. Posting defers to WP Job Manager's `job_manager_user_can_post_job` through `bpjm_user_can_post_job()`. The `post_job_user_roles` / `apply_job_user_roles` arrays were removed in 3.0.0 — do not reintroduce a second permission system. Never hook `bpjm_user_can_post_job()` back onto `job_manager_user_can_post_job`: it asks WPJM through that same filter and would recurse until memory is exhausted.
- **`bpjm_load_more_jobs` is intentionally public** (priv + nopriv), nonce-only — do not add a capability check; it would break the public infinite-scroll feed. It IS author-scoped: the request must carry the displayed profile's `user_id` (localized as `displayed_user_id`), resolves to `wp_send_json_error()` when the author is 0, and non-owners only ever get `publish` rows.
- **One canonical list of owned profile screens.** `Bp_Job_Manager_Public::bpjm_get_owned_actions()` (filter `bpjm_owned_profile_actions`) is the single source of truth for which `bp_current_action()` values are job/resume screens; `bpjm_is_owned_action()` is the predicate. `enqueue_styles()`, `enqueue_scripts()` and the `has_wpjm_shortcode` filter all read it. This list used to be duplicated four ways and had drifted, dropping assets on `jobs` (fixed 2026-07-10) and on `job-alerts` / `my-bookmarks`. **Never re-inline the array** — add to the method or use the filter.
- The bare `jobs` action is the parent nav item's own screen, reached before any subnav is selected, so it must stay in that list.
- **One canonical source for the Jobs/Resumes screen labels.** `Bp_Job_Manager_Public::bpjm_get_member_screen_label( $screen, $user_id )` produces both the subnav label and the screen heading for both tabs. There used to be four independent literals which had drifted every way possible (`"%s's Jobs"` vs `"%s Jobs"`, `ucfirst()` on one pair only, name hidden from logged-out visitors on the headings but not the nav) and one of the four was dead code, so `"%s's Jobs"` could never render while sitting in all five PO files. Never re-inline these strings; filter `bpjm_jobs_tab_name` / `bpjm_resume_tab_name` instead.
- **Never read `active_plugins` directly.** Use `bpjm_is_plugin_active()` — a bare `in_array( …, get_option( 'active_plugins' ) )` reports every network-activated add-on as inactive on every multisite subsite, which is what made add-on detection disagree with the activator's `class_exists()` checks. Core's `is_plugin_active()` does the same job but is admin-only, and most of these checks run on the front end.
- Release via `grunt dist` (-> `dist/bp-job-manager.zip`).
- Source literal hook `bpjm_profile_peronal_inf_txt` is misspelled ("peronal") — do not "fix" it; it is a public contract.

## CSS selectors (for testing/dev)
- Admin: card-panel classes from `assets/css/admin.css` (`.bpjm-btn`, `.bpjm-resource__title`, …) — the legacy `.wbcom-wrap` / `.wbcom-admin-settings-page` / `#wb_admin_plugin_name` selectors are GONE as of 2.8.5.
- Tabs rendered from `includes/admin/views/shell.php` (tab list from `Bp_Job_Manager_Admin_Panel::get_tabs()`)
- Front-end: BP profile nav items `jobs` / `resumes` and their subnav slugs (my-jobs, post-job, my-resumes, applied-jobs, add-resume)
- **Themes:** plain BuddyPress is the target. BuddyX and Reign are the tested themes; Youzify-specific styling was dropped in 3.0.0. Breakpoints are exactly three: 640 / 1024 / 1025.
- **Icons: WordPress chrome uses Dashicons, our own UI uses Lucide.** The admin *menu* icon is WP's own chrome - it stays `dashicons-lightbulb`, which is the form that follows the admin colour scheme like every other top-level menu. Everything we render ourselves uses inline Lucide SVG via `bpjm_icon( $name, $args )` (`includes/admin/class-bp-job-manager-icons.php`); do not reintroduce Dashicons there.
  The `add_menu_page()` icon argument is an icon *URL*, not one of our icon names, so a bare `'lightbulb'` renders as `<img src="lightbulb">` - a broken image, which is how it shipped between 7f57aab and 32b97e1. The audit flags that one line `use-lucide`; it is advisory and correct to ignore.

## wppqa baseline (2026-06-05)
plugin-dev-rules: 5 high `nonce-no-cap` findings — **all pre-triaged false positives** (nopriv public AJAX, activation-hook redirect, and self-meta xProfile saves). See the baseline SUMMARY for per-finding justification. rest-js-contract and wiring-completeness: skipped (no REST, no template-layer settings reads). Net real high-severity bugs from wppqa: 0.

## Quality gates (run before every commit)
`composer install` first — dev deps are required for both gates.
- **PHPStan**: `composer phpstan` (config `phpstan.neon`, level 5, WordPress stubs via `szepeviktor/phpstan-wordpress`). **Baseline is zero errors — keep it there.** BuddyPress / WP Job Manager / Resume Manager / Applications symbols are unresolvable (they are not on the analysis path) and are ignored by explicit name in `phpstan.neon`; the list is deliberately explicit rather than a blanket `.*not found` so our own typos still surface. `phpstan-bootstrap.php` declares the four `BPJM_*` runtime constants.
- **WPCS**: `composer phpcs` (config `phpcs.xml.dist`). Current baseline is **38 errors / 8 warnings**, all pre-existing and nearly all `PrefixAllGlobals.NonPrefixedHooknameFound` for third-party WPJM/BP/Resume-Manager hooks this plugin must call by their real names, plus `NonPrefixedVariableFound` for template variables injected by `get_job_manager_template()`'s `extract()`. Neither class is fixable without breaking a third-party contract. **Do not let the count rise** — diff before/after with `--report=csv` rather than reading the total.
- Generated `languages/*.l10n.php` and `phpstan-bootstrap.php` are excluded from WPCS; do not lint or hand-edit them.

## i18n workflow
`composer i18n` runs the whole chain (`make-pot` -> `make-mo` -> `make-php`). **Both** `make-mo` and `make-php` are mandatory: WP 6.5+ prefers `.l10n.php`, but Loco/WPML and `load_textdomain()` still read `.mo`, and shipping only one lets the two disagree silently.
- After changing any source string: `composer make-pot`, then `msgmerge --no-fuzzy-matching --backup=none --update <po> languages/bp-job-manager.pot`, then `msgattrib --clear-fuzzy --no-obsolete`. **Use `--clear-fuzzy`, never `--no-fuzzy`** — the latter deletes the whole entry instead of dropping the flag.
- Product names stay untranslated: `BuddyPress`, `WP Job Manager`, and the add-on names `Resume Manager` / `Applications`. The two add-on labels are rendered through `printf( esc_html__( '%s add-on', … ), 'Resume Manager' )` so the product name can never enter a PO file. `.wbcom-i18n.json` carries the `protect` list and each locale's declared register (all five are informal — fr_FR is deliberately *tu*, which diverges from the w.org fr_FR house style; that is an owner decision, do not "fix" it).
- Verify with `msgfmt -c` plus a check that `.po`, `.mo` and `.l10n.php` hold the same number of entries. (`.l10n.php` is legitimately one lower — its `messages` array excludes the PO header entry.)
- **`make-pot` runs WP-CLI's i18n audit** (the `--skip-audit` flag was removed in 2.8.6). Warnings there are real: unwrapped strings, mismatched placeholders, and the same msgid carrying two different translator comments. Keep it at zero warnings rather than re-adding the flag.
- **The bar is translation-READY, not translated.** To prove it, build a pseudo-locale from the POT where every `msgstr` becomes `«msgid»`, compile it, switch to it and read every screen: anything still plain English is either unwrapped or third-party. Two traps make this look broken when it is not — in wp-admin `determine_locale()` returns the **user's** profile language, not the site's, so set the test user's locale too; and BuddyPress / WP Job Manager strings on our screens are theirs, not ours. Product names (`BuddyPress`, `WP Job Manager`, `Resume Manager`, `Applications`, and the Discover card titles) are deliberately never translated.

## Onboarding follow-ups (out of scope of this onboarding run)
Not yet present; route to the owning skills when ready:
- CI/journey scaffold (`bin/local-ci.sh`, journeys, scale benchmark) -> `/wp-plugin-ci-setup` + `/wp-plugin-development`
- Release build gate / `.distignore` -> `/wp-plugin-release`
- QA model (`docs/qa/qa-config.json`) -> `/wp-plugin-release-qa` (then `/wp-plugin-smoke`)
- ~~Admin-wrapper migration OLD -> NEW card pattern~~ — **DONE in 2.8.5**
- ~~Remove orphaned dead code `Bp_Job_Manager_Public::bpjm_jobs_listing_show_screen()`~~ — **DONE (post-2.8.5, unreleased)**
- ~~Pre-existing WPCS `Generic.WhiteSpace.ScopeIndent` gate failure in `bpjm_load_more_jobs()`~~ — **STALE NOTE, no longer reproduces.** Measured 0 `ScopeIndent` findings on `master` before any post-2.8.5 change; it was fixed at some earlier point and this entry was never retired.
- ~~No `phpstan.neon` / `phpcs.xml.dist` / `composer.json`~~ — **DONE (post-2.8.5, unreleased)**. The repo previously carried an orphaned `composer.lock` with no `composer.json`, so no dev tooling could be installed.
- `.gitignore` still has a blanket `*.json` with only `audit/` un-ignored. `composer.json`, `package.json` and `package-lock.json` are tracked because they predate the rule, but **any new JSON needs `git add -f`** — this is the failure mode that silently kills JS translation files, so revisit the rule before adding `wp_set_script_translations()`.

## History

This file is a how-to, not a changelog. Release history lives in `readme.txt`; open bugs live on the Basecamp board. A stale "Recent changes" table used to live here and actively misled a later session about the current version.
