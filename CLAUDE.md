# Plugin: BuddyPress Job Manager

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory — 0 REST endpoints, 2 AJAX actions, 5 admin pages, 1 settings option (`bpjm_general_settings`), 1 shortcode, 0 tables, 0 cron, 0 CPTs. Use it before grepping. See also [`audit/FEATURE_AUDIT.md`](audit/FEATURE_AUDIT.md), [`audit/CODE_FLOWS.md`](audit/CODE_FLOWS.md), [`audit/ROLE_MATRIX.md`](audit/ROLE_MATRIX.md), and the wppqa baseline at [`audit/wppqa-baseline-2026-06-05/SUMMARY.md`](audit/wppqa-baseline-2026-06-05/SUMMARY.md). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.

> **Development conventions:** follow the [`wp-plugin-development`](https://example/skills/wp-plugin-development) skill for ALL code work (architecture, hooks ownership, REST patterns, escaping, security, admin UI, release packaging). This plugin predates the modern conventions; align new code to the skill, do not copy the legacy patterns below.

## Quick reference
- **Main file**: `bp-job-manager.php`
- **Version**: `2.8.5` (branch `2.8.5`)
- **Namespace**: none — class-prefix convention `Bp_Job_Manager_*`
- **Text domain**: `bp-job-manager`
- **Extends**: null (standalone; not a Free/Pro pair)
- **Requires (hard)**: BuddyPress + WP Job Manager. Resume/Application features also need Resume Manager / Applications add-ons. Self-deactivates if BP or WPJM missing.

## Admin wrapper classification: **OLD**
Uses the legacy shared wrapper `admin/wbcom/wbcom-admin-settings.php` (class `Wbcom_Admin_Settings`).
- NOT intermediate (`includes/shared-admin/class-wbcom-shared-dashboard.php` absent).
- NOT new (`includes/admin/views/shell.php` absent).
- Admin menu: hook `admin_menu` -> `Bp_Job_Manager_Admin::bpjm_add_options_page` (parent slug `wbcomplugins`, own page slug `bp-job-manager`); wrapper adds Our Plugins/Themes/Support at `admin_menu@999`.
- A migration to the NEW card-based wrapper is a tracked follow-up (see global task "Migrate 10 addons to new card wrapper").

## Key entry points
- Bootstrap: `bp-job-manager.php` (dependency gate, activation redirect)
- Orchestrator: `includes/class-bp-job-manager.php` (wires all hooks via `Bp_Job_Manager_Loader`)
- Runtime config: `includes/class-bp-job-manager-globals.php` (`global $bpjm_bp_job_manager` from option `bpjm_general_settings`)
- Admin: `admin/class-bp-job-manager-admin.php`
- Public (everything user-facing, 1810 lines): `public/class-bp-job-manager-public.php`
- Settings form: `admin/includes/bp-job-manager-general-settings.php`

## Architecture
WordPress-Plugin-Boilerplate layout (Bootstrap -> Loader -> Globals -> Admin/Public). No PHP namespace, no service container, no DB tables, no REST. Integration is hook-driven: it consumes BuddyPress + WP Job Manager / Resume Manager / Applications hooks and surfaces job/resume management inside BP member profiles. See `audit/CODE_FLOWS.md` for the six runtime flows.

## Important patterns
- **No own tables / CPTs** — operates entirely on WPJM `job_listing` / `resume` posts and BP `bp_activity`.
- **Role-based front-end gating**, capability-based admin gating. Front-end tab access is driven by `post_job_user_roles` / `apply_job_user_roles` arrays, NOT WP caps.
- **`bpjm_load_more_jobs` is intentionally public** (priv + nopriv), nonce-only — do not add a capability check; it would break the public infinite-scroll feed. It IS author-scoped: the request must carry the displayed profile's `user_id` (localized as `displayed_user_id`), resolves to `wp_send_json_error()` when the author is 0, and non-owners only ever get `publish` rows.
- Release via `grunt dist` (-> `dist/bp-job-manager.zip`).
- Source literal hook `bpjm_profile_peronal_inf_txt` is misspelled ("peronal") — do not "fix" it; it is a public contract.

## CSS selectors (for testing/dev)
- Admin: `.wbcom-wrap`, `.wbcom-admin-settings-page`, `#wb_admin_plugin_name`
- Settings tabs rendered from `admin/includes/bp-job-manager-settings-tabs.php`
- Front-end: BP profile nav items `jobs` / `resumes` and their subnav slugs (my-jobs, post-job, my-resumes, applied-jobs, add-resume)

## wppqa baseline (2026-06-05)
plugin-dev-rules: 5 high `nonce-no-cap` findings — **all pre-triaged false positives** (nopriv public AJAX, activation-hook redirect, and self-meta xProfile saves). See the baseline SUMMARY for per-finding justification. rest-js-contract and wiring-completeness: skipped (no REST, no template-layer settings reads). Net real high-severity bugs from wppqa: 0.

## Onboarding follow-ups (out of scope of this onboarding run)
Not yet present; route to the owning skills when ready:
- CI/journey scaffold (`bin/local-ci.sh`, journeys, scale benchmark) -> `/wp-plugin-ci-setup` + `/wp-plugin-development`
- Release build gate / `.distignore` -> `/wp-plugin-release`
- QA model (`docs/qa/qa-config.json`) -> `/wp-plugin-release-qa` (then `/wp-plugin-smoke`)
- Admin-wrapper migration OLD -> NEW card pattern

## Recent changes
| Date | Type | Description | Files |
|---|---|---|---|
| 2026-07-10 | bug-fix | Bug card "Add Job Button Opens Invalid Page" (10079130206), reopened: the prior fix (commit 833e1a3) pointed the WPJM `[job_dashboard]` "Add Job" button at the in-profile `jobs/post-job/` tab, but only for the permitted-roles branch of `bpjm_member_profile_jobs_tab()` - a member without a `post_job_user_roles` role (e.g. Subscriber `john`) falls into the `else` branch, which never registers a `post-job` subnav, so the button (still rendered unconditionally by WPJM, and still rewritten to that URL for any owner viewing their own dashboard) 404'd on click. Rather than hiding the button, registered the `post-job` subnav in the `else` branch too when `$is_current_user` - the click now lands on `[submit_job_form]`, which already shows "You are not authorized to post a job..." via the plugin's existing `job_manager_user_can_post_job` filter (`bpjm_restrict_user_post_job()` / `bpjm_restrict_user_post_job_message()`, both pre-existing and previously unreachable from this path). Browser-verified with `john` (subscriber): no more 404, correct restriction message shown; employer flow unaffected. | public/class-bp-job-manager-public.php |
| 2026-07-10 | bug-fix | Plugin CSS (and WPJM's own core frontend CSS) silently failed to enqueue on the profile Jobs tab for any visitor - owner or otherwise - whose role isn't in `post_job_user_roles` (e.g. Candidate/Subscriber). Root cause: `bpjm_member_profile_jobs_tab()` registers a bare `jobs` nav item (no `my-jobs` subnav) for that case, so `bp_current_action()` resolves to `'jobs'` instead of `'my-jobs'` - but `enqueue_styles()`, `enqueue_scripts()`, and `bpjm_has_wpjm_shortcode()` all checked action lists that only had `'my-jobs'`, never bare `'jobs'` (the `'jobs'` value was already correctly handled in the unrelated `get_jobs_max_num_pages()`, just missed here). `bpjm_has_wpjm_shortcode()` feeds WPJM core's `is_wpjm()`/`job_manager_enqueue_frontend_style` gate, so this also skipped WPJM's own frontend.css, not just this plugin's. Added `'jobs'` to all three checks. Browser-verified with a Candidate-role profile owner and a separate Candidate-role visitor on `/members/{user}/jobs/` - both stylesheets now enqueue. | public/class-bp-job-manager-public.php |
| 2026-07-09 | bug-fix | Bug card "Add Job button does nothing": WPJM's `[job_dashboard]` Add Job button links to `job_manager_submit_job_form_page_id` (empty href when that WPJM page is unconfigured, and it leaves the BP profile even when set). Localized `post_job_url` (own dashboard only, via `bp_core_get_userlink()`) and a JS rewrite of `.jm-dashboard__actions a.jm-ui-button` -> in-profile `jobs/post-job/` tab. Off-profile (standalone dashboard page ID) `bp_displayed_user_id()` is 0 so `post_job_url` is empty and the rewrite is skipped, leaving WPJM's native button untouched. Browser-verified. Companion card "extra required input below the description editor" triaged as WPJM core (job-submission.js 1px-textarea + native `reportValidity()`), reproduces on WPJM's own submit page - no addon change. | public/class-bp-job-manager-public.php, public/js/bp-job-manager-public.js |
| 2026-07-03 | bug-fix | Audit-verdict sweep: (1) BLOCKER anonymous AJAX data leak in `bpjm_load_more_jobs` — author now resolved from a localized `user_id` payload, request rejected via `wp_send_json_error()` when it resolves to 0, non-owners restricted to `publish` rows; (2) BLOCKER stale hardcoded `$this->version` `'1.0.0'`/`'1.0.1'` — both classes now read `BPJM_PLUGIN_VERSION`, so public CSS/JS enqueue with `?ver=2.8.5` (cache-busting works again); (3) MAJOR applied-jobs tab — real LIMIT/OFFSET pagination (`bpjm-applied-page` + `paginate_links()`, filter `bpjm_applied_jobs_per_page`, default 20), DESC order, `_prime_post_caches()` batch fetch kills the N+1; (4) MEDIUM profile-nav counts cached in `user_{ID}_job_listings`/`user_{ID}_my_resumes_count` transients (WPML-language keyed) invalidated by the existing `transition_post_status` flush + new `deleted_post` flush, WPML `SHOW TABLES` probe memoized per request; (5) fixed pre-existing `ArgumentCountError` fatal in load-more rows against WPJM 2.3+ (`job_manager_job_dashboard_column_actions` needs 2 args); (6) removed `console.log("asd")`, localized the `Loading...` string. | public/class-bp-job-manager-public.php, public/js/bp-job-manager-public.js, public/templates/bpjm-my-applied-jobs.php, includes/class-bp-job-manager.php, includes/class-bp-job-manager-globals.php, includes/class-bp-job-manager-function.php |
| 2026-07-03 | bug-fix | Dark-mode round 2 (QA re-bounce): covered WPJM `.jm-notice` (dashboard empty state + deleted/updated confirmations), the three-dot actions popover `.jm-ui-action-menu__content`, the search-input icon (dark scope re-declares WPJM token `--jm-ui-svg-search` with a light fill), and the preview panel borders (`.job_listing_preview`, `.single_job_listing .company`). Also duplicated the whole dark block for the themes' "auto" (system) mode: `@media (prefers-color-scheme: dark) [data-bx-mode="auto"]` — QA's "Desktop Mode UI Issue" card was auto mode falling back to light styles. | public/css/bp-job-manager-public.css |
| 2026-06-30 | bug-fix | Dark-mode UI: WP Job Manager core surfaces (Add Job `.jm-ui-button`, submit preview bar `.job_listing_preview_title`, confirmation `.job-manager-message`) rendered light/invisible in BuddyX/BuddyX Pro/Reign dark mode. Added explicit dark block scoped to `[data-bx-mode="dark"]`/`.buddyx-dark-theme`. | public/css/bp-job-manager-public.css |
| 2026-06-05 | onboard | Clean regeneration of audit/ artifacts + READ-FIRST CLAUDE.md | audit/*, CLAUDE.md |
