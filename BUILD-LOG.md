# BUILD-LOG — BuddyPress Job Manager: OLD wrapper → new card-panel admin

**Plugin:** bp-job-manager (Wbcom Designs - BuddyPress Job Manager)
**Branch:** 2.8.5
**Date:** 2026-06-05
**Type:** Admin-UX migration (OLD `admin/wbcom/` wrapper → jetonomy/contact-me card-panel)
**Scope:** UX-only + approved safe fixes. Zero renames of option keys, AJAX actions, nonces, hooks, meta keys, or front-end CSS classes.

---

## Files DELETED

Legacy shared wrapper + dead admin assets/partials (Playbook Part 2):

- `admin/wbcom/` (entire dir) — `wbcom-admin-settings.php` (class `Wbcom_Admin_Settings`), `templates/wbcom-{license,plugins,support}-page.php`, `assets/css/wbcom-admin-setting.css`, `assets/js/wbcom-admin-setting.js`, and `assets/imgs/`.
- `admin/css/bp-job-manager-admin.css`, `admin/css/selectize.css`
- `admin/js/bp-job-manager-admin.js`, `admin/js/selectize.min.js`
- `admin/includes/bp-job-manager-settings-tabs.php` (old `nav-tab-wrapper` dispatcher)
- `admin/includes/bp-job-manager-welcome-page.php`
- `admin/includes/bp-job-manager-general-settings.php`
- `admin/fonts/` (FontAwesome web fonts — consumed ONLY by the deleted wrapper CSS; the public stylesheet declares `font-family:"FontAwesome"` but ships no `@font-face`/`src`, so it relies on an externally-loaded FA, not these files — confirmed by grep before deletion). ~1.2 MB of dead assets removed.

Now-empty dirs `admin/css/`, `admin/js/`, `admin/includes/` removed. `admin/` retains only `index.php`, `class-bp-job-manager-admin.php` (retained service), `class-bp-job-manager-admin-feedback.php`.

## Files CREATED (8)

Controller + views (mirror buddypress-contact-me, `bpjm`/`Bp_Job_Manager_` prefix):

- `includes/admin/class-bp-job-manager-admin-panel.php` — `Bp_Job_Manager_Admin_Panel`: `get_tabs()`, `register()`, `add_menu()` (hub + single submenu), `takeover_hub_landing()` @999, `register_settings()`, `sanitize_settings()` (sentinel-guarded merge — Playbook 7.1), `enqueue_assets()` screen-scoped, `suppress_foreign_notices()`, `is_our_screen()`, `render_hub()`, `render_page()`.
- `includes/admin/views/shell.php` — header card + `wp-header-end` marker + sidebar nav + settings-form wrapper + body slot.
- `includes/admin/views/hub.php` — WB Plugins hub landing (auto-discovers peers, filters legacy wrapper helper slugs).
- `includes/admin/views/overview.php` — dependency status, role-count stats, current activity/notification config snapshot (read-only; no DB writes).
- `includes/admin/views/settings-general.php` — ports all `bpjm_general_settings` fields 1:1; native role multi-selects (selectize dropped); checkbox + array sentinels.
- `includes/admin/views/welcome.php` — Help & Support (ported from old welcome page).
- `assets/css/admin.css` — token-driven card CSS (`--bpjm-admin-*`), ported from contact-me; dead license-status block replaced with bpjm-specific helpers (status rows, toggle, role-select, resource grid). 0 stale `bcm-` selectors.
- `assets/js/admin.js` — `bpjmToast()` + `bpjmConfirm()` modal helpers (replaces native `alert`/`confirm`), ported from contact-me.

## Files MODIFIED (4)

- `includes/class-bp-job-manager.php` — `load_dependencies()`: dropped `require admin/wbcom/wbcom-admin-settings.php`, added `require includes/admin/class-bp-job-manager-admin-panel.php`. `define_admin_hooks()`: unwired the legacy class's UI methods (enqueue ×2, `bpjm_add_options_page`, `bpjm_general_settings`, `wbcom_hide_all_admin_notices_from_setting_page`); kept `bpjm_setup_admin_bar_links`, `bpjm_publish_job_listing`, `bpjm_publish_resume`; instantiate + `register()` the new panel.
- `admin/class-bp-job-manager-admin.php` — removed 9 now-dead UI methods + the `$plugin_settings_tabs` property; rewrote the class docblock to describe its narrowed post-migration scope (admin-bar links, activity writers, sanitizer). All retained public methods verified reachable (Playbook 3.1: `bpjm_publish_job_listing`/`bpjm_publish_resume`/`bpjm_setup_admin_bar_links` = 1 ref each via loader, `bpjm_sanitize_settings` = 2 refs incl. panel, `__construct` = 5; **zero dead methods**).
- `admin/class-bp-job-manager-admin-feedback.php` — **SAFE FIX (caller update, Playbook 2.1):** the review-notice `<img src>` pointed at the now-deleted `admin/wbcom/assets/imgs/bp_job_manager.png` (live code — class self-instantiates at file bottom). Replaced with a `dashicons-businessman` span so the notice no longer 404s its icon.
- `readme.txt` — added a changelog stub line under `= 2.8.5 =` for the admin-UX rebuild. **No version bump** (header/constant/readme all already `2.8.5`, no drift — per brief).

---

## Option-key preservation (old → new proof)

- `register_setting` group/name unchanged: **`bpjm_general_settings`** (was registered in `Bp_Job_Manager_Admin::bpjm_general_settings`; now in `Bp_Job_Manager_Admin_Panel::register_settings`, sanitize_callback `sanitize_settings`).
- New General view posts exactly the 6 runtime sub-keys `Bp_Job_Manager_Globals` reads: `post_job_user_roles`, `apply_job_user_roles`, `bpjm_job_post_activity`, `bpjm_resume_activity`, `bpjm_resume_at_profile`, `bpjm_app_notify` (+ two `bpjm_*_rendered_keys[]` sentinels that the sanitizer strips before save).
- Checkbox contract preserved: Globals reads via `array_key_exists()` (key present ⇒ on). Sanitizer stores `'yes'` when checked, **removes** the key when rendered-but-unchecked — both satisfy Globals' presence test exactly.
- Standalone sanitizer round-trip test (stubbed WP fns): unchecked → removed, cleared role list → `array()`, kept values preserved, sentinels stripped. **ASSERTIONS: PASS.**
- Standalone option `bpjm_job_application_pgid` and all external WPJM/Resume options are read-only and untouched (not registered, per brief).

## Safe fixes applied

1. **`wbcom_addons_cards` cap fix — N/A by deletion.** The brief flagged the nonce-only `wbcom_addons_cards_links` handler (no cap check) for a `current_user_can` add. That handler lived in `admin/wbcom/wbcom-admin-settings.php`, which is **deleted** in this migration, so the `wp_ajax_wbcom_addons_cards` registration and the handler are gone entirely — the vulnerability surface is removed rather than patched.
2. **Native `alert()`/`confirm()` → modal helpers** — new `assets/js/admin.js` ships `bpjmToast`/`bpjmConfirm`; no native dialogs.
3. **Broken wrapper image reference fixed** (feedback class, see above).
4. **`.gitignore`** — already patched at onboarding (`*.json` ignore with `audit/` + `CLAUDE.md` whitelist); left as-is.

## Left intentionally unchanged (per brief / per playbook)

- `bpjm_load_more_jobs` public nopriv AJAX — intentional public infinite-scroll; no cap added.
- 5 wppqa `nonce-no-cap` findings — pre-triaged false positives; not touched.
- Misspelled public hook literal `bpjm_profile_peronal_inf_txt` — shipped contract; preserved.
- All public/front-end code, templates, BP profile tabs, notifications, activity writers — untouched.

---

## Verification

- `php -l`: **clean** on every PHP file in the plugin (created, modified, and whole-tree sweep).
- WPCS (`wpcs` MCP) on changed files: **0 errors.** New files: `class-bp-job-manager-admin-panel.php`, all 5 views, `admin.css`/`admin.js` — 0/0. `settings-general.php` had 1 fixable alignment warning → **fixed, now clean.**
  - Pre-existing baseline warnings NOT introduced by this migration and left as-is: `includes/class-bp-job-manager.php` ×2 ("commented out code" in `define_public_hooks()` — pre-existing, untouched); `admin/class-bp-job-manager-admin.php` ×9 (direct-DB + unused-param in the retained activity writers/admin-bar method — pre-existing).
- Dangling-reference grep (Playbook 2.1) + whole-tree leftover sweep: **clean** (only explanatory comments mention `admin/wbcom/`).
- Shell `@var` ↔ `render_page()` variable contract (Playbook 12.2): **all shell vars set by renderer** (empty diff).
- Action audit (Playbook 12): no `data-action` buttons (no orphan selectors/handlers); no inline `<style>`/`<script>` in new files.
- Retained-method reachability (Playbook 3.1): **zero dead methods.**

## NOT done (per brief constraints)

- **No commit / push / merge / tag** — files staged on disk only.
- **No activate/deactivate, no DB writes.**
- **Live browser smoke test NOT run** — WP Job Manager is not installed/active on this sandbox and `bp-job-manager` is inactive; the plugin's hard-dependency gate self-deactivates without WPJM, and the brief forbids activation. Admin-page render therefore could not be exercised in-browser. Static + standalone verification stands in (see above).

## TODOs / needs human eyes

1. **Browser verification still owed** (Playbook Part 13): on a site with BuddyPress **and** WP Job Manager active, activate bp-job-manager and confirm — WB Plugins hub renders the card grid; the Job Manager submenu shows once; Overview/General/Help tabs render; **saving General round-trips** to `bpjm_general_settings` with the same key shape (toggle a checkbox off, save, confirm the profile Jobs/Resumes tab behaviour follows); 390px sidebar collapse; zero console errors; `debug.log` clean. Resume/Application toggles only appear when those WPJM add-ons are active.
2. **Pre-existing inline `<style>` in `admin/class-bp-job-manager-admin-feedback.php`** (lines ~171–271) — review-prompt notice ships an inline `<style>` block (Part 7.7 violation). Pre-existing, out of this migration's scope; flag for a future code-quality pass.
3. **Pre-existing commented-out hook code** in `includes/class-bp-job-manager.php` `define_public_hooks()` (the `bp_activity_check_activity_types` / `bp_register_activity_actions` lines) — out of scope; candidate for a future cleanup.
