# Feature Audit — BuddyPress Job Manager 2.8.5

> Derived from [`manifest.json`](manifest.json). Branch `2.8.5`. Generated 2026-06-05 by wp-plugin-onboard.
> Bridges WP Job Manager (+ Applications + Resume Manager) into BuddyPress member profiles.

## Hard dependencies
Plugin self-deactivates unless **BuddyPress** AND **WP Job Manager** are active
(`bp-job-manager.php:103-112`). Resume features additionally need **WP Job
Manager - Resume Manager**; application notifications need **WP Job Manager - Applications**.

## 1. Frontend tabs / pages (BuddyPress profile nav)

| Tab | Gated by | Sub-views | Source |
|---|---|---|---|
| Jobs | role ∈ `post_job_user_roles` | Post Job, My Jobs, Job Listings, Bookmarked Jobs, Job Alerts | `public/class-bp-job-manager-public.php::bpjm_member_profile_jobs_tab` (`bp_setup_nav`) |
| Resumes | `bpjm_resume_at_profile=yes` AND role ∈ `apply_job_user_roles` | Add Resume, My Resumes, Applied Jobs | `bpjm_member_profile_resumes_tab` |

Admin-bar mirrors of these tabs are added in `Bp_Job_Manager_Admin::bpjm_setup_admin_bar_links` (`bp_setup_admin_bar @70`), with live count badges via `bpjm_member_profile_jobs_count` / `bpjm_member_profile_resumes_count`.

Virtual page: **Job Application** — template injected via `page_template` filter (`bpjm_job_application_page`); page ID stored in option `bpjm_job_application_pgid`.

## 2. AJAX handlers

| Action | File:line | Nonce | Capability | JS caller |
|---|---|---|---|---|
| `bpjm_load_more_jobs` (priv + nopriv) | `public/class-bp-job-manager-public.php:70-71` | `bpjm_load_jobs_nonce` (key `ajax_nonce`, minted :163) | none — public infinite scroll, read-only | `public/js/bp-job-manager-public.js` |
| `wbcom_addons_cards` | `admin/wbcom/wbcom-admin-settings.php:32` | `wbcom_admin_setting_nonce` (key `nonce`) | admin-area helper; echoes posted string only | `admin/wbcom/assets/js/wbcom-admin-setting.js` |

## 3. REST endpoints
_None._

## 4. Admin pages / settings

Parent menu **WB Plugins** (`wbcomplugins`, top-level, `dashicons-lightbulb`, pos 59) — created only if no sibling Wbcom plugin already created it. See ROLE_MATRIX / manifest `admin_menu_registration`.

| Page | Slug | Parent | Cap | Source |
|---|---|---|---|---|
| WB Plugins / General | `wbcomplugins` | top-level | `manage_options` (filter `bpjm_menu_page_capability`) | admin class :142-143 |
| Job Manager | `bp-job-manager` | `wbcomplugins` | `manage_options` | admin class :145 |
| Our Plugins | `wbcom-plugins-page` | `wbcomplugins` | `manage_options` | wrapper :212 |
| Our Themes | `wbcom-themes-page` | `wbcomplugins` | `manage_options` | wrapper :220 |
| Support | `wbcom-support-page` | `wbcomplugins` | `manage_options` | wrapper :228 |

**Settings option `bpjm_general_settings`** (group `bpjm_general_settings`, registered admin class :213, sanitize `bpjm_sanitize_settings` :451, read in `includes/class-bp-job-manager-globals.php`). Tabs: Welcome (`bpjm-welcome`), General (`bp-job-manager`).

Sub-keys (form `admin/includes/bp-job-manager-general-settings.php`):
- `apply_job_user_roles[]`, `post_job_user_roles[]`
- `bpjm_resume_at_profile`, `bpjm_resume_activity`, `bpjm_job_post_activity`, `bpjm_app_notify`

Standalone option: `bpjm_job_application_pgid` (int page id). External options read: `job_manager_per_page`, `job_manager_job_dashboard_page_id`, `resume_manager_*`, `date_format`, `active_plugins`.

## 5. Shortcodes
| Tag | Handler | Scope |
|---|---|---|
| `wbcom_admin_setting_header` | `Wbcom_Admin_Settings::wbcom_admin_setting_header_html` | admin-only header (do_shortcode in settings page) |

No front-end shortcodes — front-end surfaces are BP profile tabs + WPJM dashboard column/action filters.

## 6. Content types
_No custom CPTs or taxonomies._ Operates on WPJM's `job_listing` / `resume` post types.

## 7. JS modules
- `public/js/bp-job-manager-public.js` — infinite scroll (AJAX `bpjm_load_more_jobs`).
- `admin/js/bp-job-manager-admin.js`, `admin/js/selectize.min.js` — role multiselect on settings.
- `admin/wbcom/assets/js/wbcom-admin-setting.js` — wrapper header / promo cards.

## 8. CSS modules
- `public/css/bp-job-manager-public.css`
- `admin/css/bp-job-manager-admin.css`, `admin/css/selectize.css`
- `admin/wbcom/assets/css/wbcom-admin-setting.css`
- FontAwesome icon font bundled in `admin/fonts/`.

## 9. Email / notifications
No emails. BuddyPress notification on new job application — `bpjm_add_bp_notification_for_job_post` (gated by `bpjm_app_notify`), component registered via `bpjm_comment_get_registered_components`, rendered via `bpjm_job_application_notifications`.

## 10. Cron
_None._

## 11. DB tables
_None created._ Reads WP core posts + BuddyPress `{prefix}bp_activity` (for posting job/resume activity items; presence-checked before write in `bpjm_publish_job_listing`).

## 12. Integrations
- **WP Job Manager** — dashboard columns/actions, edit permissions, post-job restriction by role, shortcode-action handler control.
- **Resume Manager** — candidate dashboard scoping, resume actions, contact-details private-message link.
- **WPJM Applications** — new-application BP notifications.
- **Youzify** — alternate xProfile nonce path in `custom_bp_init`.
- **WPML** — `wpml_current_language` consulted.
- **bp-nouveau** — nav link fixes (`bpjm_nouveau_link_fix`, `bpjm_legacy_link_fix`).

## Capabilities
`manage_options` (all admin pages, filterable via `bpjm_menu_page_capability`); `activate_plugins` (dependency notice gate). No custom caps. Front-end tab access is **role-based** (`post_job_user_roles` / `apply_job_user_roles`), not capability-based.

## Extension hooks fired (for site devs)
Filters: `bpjm_menu_page_capability`, `bpjm_profile_*_txt` (resume labels), `bpjm_resume_fields_custom_slug/text`, `bpjm_resumes_*` (nav), `bp_activity_new_job_post_action`. Actions: `wbcom_add_header_menu`, `wbcom_add_plugin_license_code` (wrapper slots), dynamic `job_manager_job_dashboard_column_<key>` / `resume_manager_candidate_dashboard_column_<key>` re-emits.

> Note: source literal `bpjm_profile_peronal_inf_txt` is misspelled ("peronal") — preserved as-is in the manifest because changing it would break any existing consumer.
