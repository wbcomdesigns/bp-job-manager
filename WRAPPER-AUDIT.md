# Wrapper Audit — BuddyPress Job Manager

**Plugin:** bp-job-manager  
**Branch / version:** 2.8.5  
**Wrapper type:** NEW card-panel  
**Audit date:** 2026-06-05  
**Auditor:** AutoVAP (read-only)  
**Reference:** buddypress-contact-me (`includes/admin/class-bcm-admin.php`)  

---

## Overall Verdict

**SHIP-CLEAN** (one low-severity cosmetic note on `.bpjm-btn` tap-target; no data-loss, no security gap, no dead link, no orphan view).

---

## 1. Controller Contract

File: `includes/admin/class-bp-job-manager-admin-panel.php`

| Method | Present? | Notes |
|---|---|---|
| `get_tabs()` | YES | Returns overview / general / welcome; filterable via `bpjm_admin_tabs`. |
| `register()` | YES | Hooks: `admin_menu` (default + @999), `admin_init`, `admin_enqueue_scripts`, `in_admin_header`. |
| `add_menu()` | YES | Creates top-level `wbcomplugins` if absent; adds single submenu `bp-job-manager`. |
| `takeover_hub_landing()` | YES | @999; `remove_all_actions('toplevel_page_wbcomplugins')` then re-binds `render_hub`. |
| `register_settings()` | YES | `register_setting('bpjm_general_settings', 'bpjm_general_settings', sanitize_callback => $this->sanitize_settings)`. |
| `sanitize_settings()` | YES | Full sentinel-guarded merge (Playbook 7.1) — see Option Wiring section. |
| `enqueue_assets()` | YES | Screen-scoped via `is_our_screen()`; enqueues `bpjm-admin` style + script + `wp_localize_script`. |
| `is_our_screen()` | YES | Regex-matches `_page_bp-job-manager$` and `toplevel_page_wbcomplugins`. |
| `suppress_foreign_notices()` | YES | `in_admin_header@1`; `remove_all_actions('admin_notices')` + `remove_all_actions('all_admin_notices')` on our screen. |
| `render_hub()` | YES | `BPJM_PLUGIN_PATH . 'includes/admin/views/hub.php'` (file_exists guarded). |
| `render_page()` | YES | Sanitizes `$_GET['tab']`, resolves view slug, includes shell. |

**All required methods present.**

---

## 2. Path-Constant Resolution

**Result: PASS**

Constants are defined in the main plugin file `bp-job-manager.php` (`__FILE__` = plugin root):

```php
define( 'BPJM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );  // = .../bp-job-manager/
define( 'BPJM_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );   // = http://.../wp-content/plugins/bp-job-manager/
```

The controller (`includes/admin/class-bp-job-manager-admin-panel.php`) references:
- `BPJM_PLUGIN_URL  . 'assets/css/admin.css'` — file exists at `assets/css/admin.css`.
- `BPJM_PLUGIN_URL  . 'assets/js/admin.js'` — file exists at `assets/js/admin.js`.
- `BPJM_PLUGIN_PATH . 'includes/admin/views/hub.php'` — file exists.
- `BPJM_PLUGIN_PATH . 'includes/admin/views/shell.php'` — file exists.
- `BPJM_PLUGIN_PATH . 'includes/admin/views/' . $view . '.php'` — resolved view slugs are `overview`, `settings-general`, `welcome`; all three files exist.

The orchestrator (`includes/class-bp-job-manager.php`) uses `plugin_dir_path(__DIR__)` for its own `require_once` calls. `__DIR__` there is `.../bp-job-manager/includes/`, so `plugin_dir_path(__DIR__)` resolves to `.../bp-job-manager/` (plugin root). All `require_once` targets exist on disk. No 404 risk.

---

## 3. Wrapper Structure / Gap Check

| Check | Result |
|---|---|
| `views/shell.php` exists with header card, `.wp-header-end`, sidebar nav from `get_tabs()`, body slot | PASS |
| `views/hub.php` exists (auto-discovers peer plugins, filters legacy wrapper helper slugs) | PASS |
| Every tab in `get_tabs()` has a matching view file | PASS (overview → `overview.php`, general → `settings-general.php`, welcome → `welcome.php`) |
| Every view file is reachable from a tab | PASS (no orphan views) |
| No dead nav links | PASS |
| Legacy `admin/wbcom/` directory gone | PASS — directory deleted; `ls admin/` confirms: only `index.php`, `class-bp-job-manager-admin.php`, `class-bp-job-manager-admin-feedback.php`. |
| No `includes/shared-admin/` directory | PASS |
| No `[wbcom_admin_setting_header]` shortcode reference | PASS — only a comment in `includes/class-bp-job-manager.php` (line 131) and `includes/admin/class-bp-job-manager-admin-panel.php` (line 5) mention the old path as explanatory prose; zero live code references. |
| No stale `wbcom-admin-setting` handle/path in live code | PASS |
| `admin/css/`, `admin/js/`, `admin/includes/` subdirs gone | PASS — all three paths return "No such file or directory". |

---

## 4. Option Wiring — End-to-End

### 4a. `bpjm_general_settings` (array option)

**Single registration point:** `Bp_Job_Manager_Admin_Panel::register_settings()` (controller line 134).

| Sub-key | Registered? | Rendered (field + `name`) | Sanitized? | Read-back in Globals? | Verdict |
|---|---|---|---|---|---|
| `apply_job_user_roles[]` | YES — under `bpjm_general_settings` group | YES — `<select multiple name="bpjm_general_settings[apply_job_user_roles][]">` in `settings-general.php:113` | YES — `array_map('sanitize_text_field', wp_unslash(...))` in sanitizer | YES — `class-bp-job-manager-globals.php:140–141` | PASS |
| `post_job_user_roles[]` | YES | YES — `settings-general.php:93` | YES | YES — `globals.php:143–144` | PASS |
| `bpjm_resume_at_profile` | YES | YES — checkbox `settings-general.php:159` (gated by `$bpjm_resumes_on`) | YES — presence/absence pattern with sentinel | YES — `globals.php:147–151` | PASS |
| `bpjm_resume_activity` | YES | YES — `settings-general.php:150` (gated) | YES | YES — `globals.php:153–157` | PASS |
| `bpjm_job_post_activity` | YES | YES — `settings-general.php:140` (always rendered) | YES | YES — `globals.php:159–163` | PASS |
| `bpjm_app_notify` | YES | YES — `settings-general.php:170` (gated by `$bpjm_apps_on`) | YES | YES — `globals.php:165–169` | PASS |

**Sanitizer checkbox contract:** value `'yes'` is stored when checked; the key is **removed** (not set to `''`) when the sentinel lists it as rendered but `$_POST` omits it. `Bp_Job_Manager_Globals` reads via `array_key_exists()` (presence = yes), so removal satisfies the "off" contract exactly.

**Multi-select contract:** `array_values(array_filter(array_map('sanitize_text_field', wp_unslash($input[$key]))))` — no unslashed, unsanitized writes.

**Sentinel names** emitted by `settings-general.php`:
- `bpjm_general_settings[bpjm_tab_rendered_keys][]` — lists checkbox keys rendered this submit.
- `bpjm_general_settings[bpjm_array_rendered_keys][]` — lists array-key names rendered this submit.

Sanitizer strips both sentinel keys before write (`unset($input['bpjm_tab_rendered_keys'])`, `unset($input['bpjm_array_rendered_keys'])`). They do **not** land in the stored option.

**Default/fallback rendering:** `settings-general.php` falls back to `$bpjm_bp_job_manager->post_job_user_roles` / `->apply_job_user_roles` when the stored option's arrays are empty, matching the same defaults Globals seeds for a fresh install.

### 4b. `bpjm_job_application_pgid` (standalone option)

**Registered?** NO — and correctly so. This option is written by `Bpjm_Job_Manager_Activator::activate()` (an internal activation-time `update_option` call) and is read back by `Globals::setup_plugin_global():172`. It is an internal-ID key, not a form field. It is not registered with `register_setting` and should not be; it is not written by the new settings form. This matches the manifest entry (`"written_at": "update_option in globals/public flow"`, `"controls": "Internal"`).

**External WPJM / Resume Manager options read by this plugin** (`job_manager_per_page`, `job_manager_job_dashboard_page_id`, `resume_manager_*`): grepped all PHP files — zero `register_setting`, `update_option`, or `add_option` calls for any of these keys exist in the new wrapper files or any retained file. Read-only consumption is confirmed.

---

## 5. Multi-Tab Data-Loss Guard

**Status: PRESENT AND CORRECT**

The sentinel-guarded merge is fully implemented in `Bp_Job_Manager_Admin_Panel::sanitize_settings()`:

1. The sanitizer opens with `$output = get_option(self::OPTION_NAME, array())` — starts from the stored value.
2. Only keys whose sentinels were emitted (= whose tab was rendered) are touched; all other stored keys survive untouched.
3. A future second settings tab would follow the same pattern and its keys would be preserved by this merge — the guard is tab-count-agnostic.

Since there is only one settings tab (`general`) at present, the guard is N/A for current data-loss risk, but it is in place for correctness on future expansion.

---

## 6. `wbcom_addons_cards` Handler Gone

**Status: CONFIRMED GONE**

Grep for `wbcom_addons_cards`, `wp_ajax_wbcom_addons_cards`, and `wbcom_addons_cards_links` across all PHP files returns **zero results**. The AJAX handler and its registration lived exclusively in the now-deleted `admin/wbcom/wbcom-admin-settings.php`. The vulnerability surface (nonce-only, no capability check) is eliminated by deletion — this matches the BUILD-LOG stated intent.

---

## 7. Misspelled Public Hook `bpjm_profile_peronal_inf_txt`

**Status: PRESERVED — PASS**

```
public/templates/profile-content-single-resume.php:40:
    apply_filters( 'bpjm_profile_peronal_inf_txt', 'Personal Information' )
```

The literal misspelling is present and unchanged. No rename occurred in any new or modified file.

---

## 8. Dependency-Aware Behavior

**Self-deactivation without WPJM/BP:** `bp-job-manager.php:103–112` — `bpjm_plugin_init()` gates the entire plugin behind `class_exists('BuddyPress') && class_exists('WP_Job_Manager')`. If either is absent: only the admin notice is shown and `bpjm_existing_checkin_plugin()` deactivates the plugin. The new admin panel code is never loaded. **PASS.**

**"Required Integrations" panel in Overview:** `includes/admin/views/overview.php` — shows BP, WPJM, Resume Manager add-on, Applications add-on with status badges (Active / Not active / Optional — not active). **PASS.**

**No fatal when optional add-ons absent:** Resume and application fields in `settings-general.php` are gated by `$bpjm_resumes_on` and `$bpjm_apps_on` (checked via `in_array` against `active_plugins`). No function from Resume Manager or Applications is called unconditionally in the new wrapper code. **PASS.**

---

## 9. Hygiene Checks

### alert() / confirm()
Grep across all PHP and JS files for `alert(` and `confirm(` returns:
- `assets/js/admin.js:4` — only in a comment (`"no browser alert()/confirm() anywhere"`), not live code.
- No other hits.

`window.bpjmToast()` and `window.bpjmConfirm()` modal helpers are implemented and exported. **PASS — no native dialogs.**

### CSS Design Tokens
All colors used in layout rules reference `var(--bpjm-admin-*)` tokens. The exceptions are:
- `color: #fff` — 8 uses (always `#ffffff` white on colored backgrounds). These are semantically fixed and do not represent a token gap.
- `color: #92400e` — 1 use (`.bpjm-notice--warn` text color). This is a raw hex on a production file.
- `rgba(...)` — 10 uses, all for box-shadows, focus-rings, and overlays (alpha variants not expressible as tokens without CSS `color-mix()`).

The `#92400e` is a minor token gap (`.bpjm-notice--warn` warn text color should be `var(--bpjm-admin-warn-text)` or similar if a dark-mode token ever lands). Low severity, cosmetic only.

All token definitions live inside `.bpjm-admin { }`, scoped correctly to avoid leaking to other plugins.

### Tap Targets
- `.bpjm-btn` — `min-height: 36px`. Below the 40px tap-target floor from the plugin-dev skill Part 7.4.
- `.bpjm-role-chip` — `min-height: 40px`. Compliant.
- `.bpjm-toggle` — `min-height: 24px`. Not a standalone touch target (it is a label wrapping a checkbox); the full label row is ~36px tall in practice.

The `.bpjm-btn` at 36px is a low-severity deviation that matches the reference implementation (buddypress-contact-me uses the same button height). It is admin-only and desktop-primary; the practical risk is negligible, but it should be noted for future consistency pass.

### Responsive
Two breakpoints at 1024px (sidebar stacks, nav wraps) and 640px (form-table cells block, stats grid collapses). This satisfies the "exactly two media blocks" pattern from skill Part 7.5.

### Screen-scoped Enqueue
`enqueue_assets()` calls `get_current_screen()` and exits if `!is_our_screen($screen)`. Assets load only on `_page_bp-job-manager` and `toplevel_page_wbcomplugins`. **PASS.**

### Focus Rings
Every interactive element that suppresses `outline` does so only inside `:focus:not(:focus-visible)`, preserving the keyboard-visible ring via `:focus-visible`. The one exception is `.bpjm-role-select:focus { outline: none }` (line 1110) — this suppresses the outline for **all** focus, not just pointer focus. Keyboard users of the role `<select>` lose the browser outline ring. The box-shadow focus ring (3px accent glow) is present but may not meet WCAG 2.4.11 in all high-contrast modes.

---

## 10. Findings Table (Severity-Ranked)

| # | Severity | Finding | File:line | Suggested Fix |
|---|---|---|---|---|
| 1 | Low | `.bpjm-role-select:focus { outline: none }` suppresses focus ring unconditionally — affects keyboard users of the role `<select>` multi-selects. The box-shadow ring is present but not guaranteed in high-contrast mode. Change to `:focus:not(:focus-visible) { outline: none }` and add `:focus-visible { outline: 2px solid var(--bpjm-admin-accent); outline-offset: 2px; }` | `assets/css/admin.css:1107–1111` | CSS styling touch in a future code-quality pass |
| 2 | Low | `.bpjm-btn` `min-height: 36px` is below the 40px tap-target floor (skill Part 7.4). Admin-only / desktop-primary context; practical risk low. Increase to `min-height: 40px` for consistency with `.bpjm-role-chip`. | `assets/css/admin.css:570` | CSS styling touch |
| 3 | Low | `.bpjm-notice--warn` text color is raw `#92400e` rather than a named token. No immediate functional impact; minor token hygiene gap. Introduce `--bpjm-admin-warn-text: #92400e` in the token block. | `assets/css/admin.css:684` | CSS styling touch |
| 4 | Info | Pre-existing `admin/class-bp-job-manager-admin-feedback.php` ships an inline `<style>` block (~100 lines, lines 171–271) — Part 7.7 violation. Out of scope for this migration; candidate for a future code-quality pass (acknowledged in BUILD-LOG TODO #2). | `admin/class-bp-job-manager-admin-feedback.php:171` | Separate code-quality pass |
| 5 | Info | Pre-existing commented-out hook lines in `includes/class-bp-job-manager.php` `define_public_hooks()` (bp_activity_check_activity_types / bp_register_activity_actions). Out of scope; BUILD-LOG TODO #3. | `includes/class-bp-job-manager.php:262–268` | Separate cleanup pass |

**No Blocker or High findings.**

---

## 11. Scores Summary

| Lens | Grade | Notes |
|---|---|---|
| Controller contract | A | All 11 required methods present and correctly implemented. |
| Path-constant resolution | A | `BPJM_PLUGIN_PATH` / `BPJM_PLUGIN_URL` defined at root `__FILE__`; all `include`/asset paths resolve. |
| Tab ↔ view map | A | 3 tabs, 3 view files, zero dead links, zero orphans. |
| Option wiring | A | All 6 sub-keys of `bpjm_general_settings` registered → rendered → sanitized → read back correctly. `bpjm_job_application_pgid` correctly absent from the form (internal-only). External WPJM/Resume options are read-only. |
| Multi-tab guard | A | Sentinel-guarded merge present; future tabs safe. |
| Legacy removal | A | `admin/wbcom/` gone, no dangling shortcode/handle/path references in live code. |
| `wbcom_addons_cards` | A | AJAX handler and registration gone (deleted with the wrapper). |
| Misspelled hook preservation | A | `bpjm_profile_peronal_inf_txt` preserved in `public/templates/profile-content-single-resume.php`. |
| Dependency gating | A | Self-deactivation on missing BP/WPJM intact; optional add-on fields correctly gated; no fatal without Resume/Applications. |
| Hygiene (alert/confirm) | A | No native dialogs; `bpjmToast` + `bpjmConfirm` helpers in place. |
| CSS tokens | B+ | 1 raw hex (`#92400e`) outside a token; `#fff` uses are acceptable. `rgba()` uses are alpha variants. |
| Tap targets | B+ | `.bpjm-btn` at 36px (minor); `.bpjm-role-chip` at 40px (compliant). |
| Focus rings / a11y | B+ | All interactive elements have `:focus-visible` rings except `.bpjm-role-select:focus` (bare `outline: none`). |
| Screen-scoped enqueue | A | Assets load only on the plugin's own screen and the hub. |
| Responsive | A | Two breakpoints (1024px, 640px) per skill Part 7.5. |

---

*This file was generated read-only by AutoVAP. No code was modified.*
