# Code Flows — BuddyPress Job Manager 2.8.5

> **Written against 2.8.5.** Structure changed substantially in 3.0.0: the role arrays, the
> resume-on-profile flow and two re-rendered dashboards were removed, and the activator and
> deactivator classes were deleted. Treat this as history and verify against the code.
> Current state: [`HANDOFF-3.0.0.md`](HANDOFF-3.0.0.md).

> Derived from [`manifest.json`](manifest.json). Generated 2026-06-05 by wp-plugin-onboard.

## Bootstrap

```
bp-job-manager.php
  defines BPJM_PLUGIN_PATH/URL/VERSION
  register_activation_hook   -> Bpjm_Job_Manager_Activator::activate
  register_deactivation_hook -> Bp_Job_Manager_Deactivator::deactivate
  require includes/class-bp-job-manager.php
  add_action('plugins_loaded', 'bpjm_plugin_init')
       │
       └─ bpjm_plugin_init():
            if !BuddyPress || !WP_Job_Manager  -> admin_notices + self-deactivate
            else -> new Bp_Job_Manager()->run()  (Loader dispatches all hooks)
                    add_filter plugin_action_links (Settings/Support)
```

`activated_plugin` -> `bpjm_activation_redirect_settings` (nonce-guarded) sets `_bpjm_is_new_install` transient; `admin_init` -> `bpjm_do_activation_redirect` consumes it and redirects to the settings page.

Hook wiring lives in `includes/class-bp-job-manager.php` (`define_admin_hooks` / `define_public_hooks`) via `Bp_Job_Manager_Loader`. Runtime config (`global $bpjm_bp_job_manager`) comes from `Bp_Job_Manager_Globals` reading `bpjm_general_settings`.

## Flow A — Admin settings page

```
admin_menu -> Bp_Job_Manager_Admin::bpjm_add_options_page
   add_menu_page 'wbcomplugins' (if not already present, cap via bpjm_menu_page_capability)
   add_submenu_page 'bp-job-manager' -> bpjm_admin_settings_page()
admin_menu@999 -> Wbcom_Admin_Settings::wbcom_admin_additional_pages (Our Plugins/Themes/Support)
admin_init -> bpjm_general_settings()
   register_setting('bpjm_general_settings', sanitize=bpjm_sanitize_settings)
   add_settings_section welcome + general

bpjm_admin_settings_page()  (tab = ?tab, default bpjm-welcome)
   do_shortcode('[wbcom_admin_setting_header]')   -> wrapper header
   bpjm_plugin_settings_tabs()                     -> admin/includes/bp-job-manager-settings-tabs.php
   do_settings_sections($tab)
       welcome  -> admin/includes/bp-job-manager-welcome-page.php
       general  -> admin/includes/bp-job-manager-general-settings.php (role multiselect via selectize + checkboxes)
```

Save: WP Settings API POST -> `bpjm_sanitize_settings` -> option `bpjm_general_settings`. Read back on next load through `Bp_Job_Manager_Globals::setup_plugin_global()`.

Required settings: none to function (sensible role defaults). Roles: `manage_options`.

## Flow B — Member "Jobs" / "Resumes" profile tabs

```
bp_setup_nav -> bpjm_member_profile_jobs_tab / bpjm_member_profile_resumes_tab
   role check against post_job_user_roles / apply_job_user_roles (+ bpjm_resume_at_profile)
   register BP nav + subnav -> *_show_screen() -> *_to_show_title()/_to_show_content()
       content pulls WPJM/Resume Manager shortcodes & templates (public/templates/*.php)
bp_setup_admin_bar@70 -> bpjm_setup_admin_bar_links (mirror links + count badges)
```

Roles: any logged-in user whose role is in the configured arrays. Deps: BuddyPress (+ Resume Manager for Resumes tab).

## Flow C — Infinite scroll (My Jobs)

```
wp_enqueue_scripts -> Public::enqueue_scripts
   wp_localize_script(... ajax_nonce = wp_create_nonce('bpjm_load_jobs_nonce'))   (:163)
[user scrolls] public/js/bp-job-manager-public.js
   POST admin-ajax.php action=bpjm_load_more_jobs (page_no, ajax_nonce)
wp_ajax_ / wp_ajax_nopriv_ bpjm_load_more_jobs -> Public::bpjm_load_more_jobs (:204)
   check_ajax_referer('bpjm_load_jobs_nonce','ajax_nonce')
   WP_Query published job_listing, per_page = get_option('job_manager_per_page')
   echo rendered rows -> JS appends
```

Auth: nonce only (intentionally public; read-only over published jobs). First page is server-rendered, so JS-off degrades gracefully.

## Flow D — Activity + notifications on publish

```
publish_job_listing@999 -> Admin::bpjm_publish_job_listing
   if bpjm_job_post_activity=yes -> bp_activity_add (type bpjm_job_post; dedup SELECT on bp_activity)
publish_resume@999     -> Admin::bpjm_publish_resume   (if bpjm_resume_activity=yes)
new_job_application / job_manager_applications_new_job_application
   -> Public::bpjm_add_bp_notification_for_job_post (if bpjm_app_notify=yes)
bp_notifications_get_notifications_for_user -> bpjm_job_application_notifications (render)
```

## Flow E — WPJM dashboard integration (filters/actions)

```
job_manager_get_dashboard_jobs_args / resume_manager_get_dashboard_resumes_args
   -> scope dashboard to the viewed member's user_id
job_manager_job_dashboard_columns / _column_actions, *_my_job_actions
resume_manager_my_resume_actions, resume_manager_contact_details
   -> Public adds/edits columns, action links, private-message link
job_manager_user_can_post_job / submit_job_form_show_signin / submit_job_form_disabled
   -> Public::bpjm_restrict_user_post_job (gate posting by role)
```

## Flow F — Front-end resume field settings save

```
bp_init -> Public::custom_bp_init
   if $_POST xprofile-settings-submit | bpjm-resume-fields-settings-submit:
      check_admin_referer( Youzify ? bpjm_resume_fields_settings_nonce : bp_xprofile_settings )
      update_user_meta(bp_loggedin_user_id(), 'bpjm_profile_resume_show_id', ...)   (self-meta only)
```

Auth: per-user — writes only the current logged-in user's own meta; nonce-gated.

## Key files

| File | Role |
|---|---|
| `bp-job-manager.php` | Bootstrap, dependency gate, activation redirect |
| `includes/class-bp-job-manager.php` | Orchestrator — wires all admin/public hooks |
| `includes/class-bp-job-manager-loader.php` | Hook registry + `run()` |
| `includes/class-bp-job-manager-globals.php` | Settings -> typed runtime config (`global $bpjm_bp_job_manager`) |
| `includes/class-bp-job-manager-function.php` | Helpers (profile counts) |
| `admin/class-bp-job-manager-admin.php` | Menu, settings, admin bar, activity-on-publish |
| `admin/wbcom/wbcom-admin-settings.php` | OLD shared Wbcom admin wrapper |
| `public/class-bp-job-manager-public.php` | All BP tabs, dashboards, AJAX, notifications (1810 lines) |

## AJAX chain table

| Action | JS | PHP | Nonce | Output |
|---|---|---|---|---|
| `bpjm_load_more_jobs` | bp-job-manager-public.js | Public::bpjm_load_more_jobs | bpjm_load_jobs_nonce/ajax_nonce | HTML rows |
| `wbcom_addons_cards` | wbcom-admin-setting.js | Wbcom_Admin_Settings::wbcom_addons_cards_links | wbcom_admin_setting_nonce/nonce | echoed string |
