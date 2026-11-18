# BuddyPress Job Manager - Capabilities

> Buyer-level roll-up of what this plugin does, derived from [`audit/manifest.json`](audit/manifest.json) and verified against code on 2026-11-17 (release/3.0.1). Regenerate with `/wp-plugin-onboard --refresh`. Maturity lives in the manifest, not in this prose.

- **Version:** 3.0.1
- **Slug:** `bp-job-manager` - **Text domain:** `bp-job-manager` - **Prefix:** `Bp_Job_Manager_` / `bpjm_` (no PHP namespace)
- **Requires:** WordPress 6.5+ - PHP 8.0+ - WP Job Manager (hard, `Requires Plugins` header) - BuddyPress or BuddyBoss Platform (hard, checked at runtime)
- **Optional add-ons:** WP Job Manager Resume Manager, Applications, Bookmarks, Alerts - each unlocks one surface, none is required

## At a glance

| Surface | Count |
|---|---|
| Admin pages | 2 (own page under the shared WB Plugins menu, plus the hub landing) |
| Admin tabs | 4 (Overview, General, Help & Support, Discover) |
| Settings | 3 toggles in one option (`bpjm_general_settings`) |
| Member profile tabs | 2 (Jobs, Resumes) with up to 7 subnav screens |
| BuddyPress activity types | 2 (`bpjm_job_post`, `bpjm_resume_publish`) |
| BuddyPress notification actions | 2 (`applied_job`, `application_status`) |
| Plugin-own hooks fired | 10 (all filters) |
| REST routes / AJAX actions / blocks / shortcodes / CPTs / tables / cron | 0 (none, by design) |

## Capability questions

### Jobs on the member profile

| Can it... | Status | Delivered by |
|---|---|---|
| Put a Jobs tab on member profiles? | YES | `Bp_Job_Manager_Public::bpjm_member_profile_jobs_tab` (own profile always; someone else's only when they have jobs) |
| Let a member post a job without leaving the profile? | YES | Post Job subnav, `bpjm_post_job_show_screen`; WP Job Manager decides who may post |
| Let a member edit, duplicate, continue, relist or renew their jobs? | YES | My Jobs dashboard plus `bpjm_handle_submission_actions` (WPJM nonce and edit check) |
| Show a visitor another member's open jobs? | YES | Jobs tab on a profile that has jobs |
| Show a job count badge on the tab? | YES | `bpjm_member_profile_jobs_count` (cached one hour, flushed on post status change) |
| Keep long job lists usable? | YES | `bpjm_pagination_args`, out-of-range pages return to page one with a notice |
| Restrict posting by role? | NO | Roles were removed in 3.0.0; WP Job Manager's own gate (`job_manager_user_can_post_job`) decides |

### Resumes (needs Resume Manager)

| Can it... | Status | Delivered by |
|---|---|---|
| Put a Resumes tab on member profiles? | YES | `bpjm_member_profile_resumes_tab` (own profile; others only if they have resumes and the visitor may browse resumes) |
| Let a candidate add and manage several resumes in the profile? | YES | My Resumes and Add Resume subnavs, rendered by Resume Manager |
| Relist an expired resume from the profile? | YES | `bpjm_handle_resume_relist` (Resume Manager nonce and edit check) |
| Let an employer message a candidate? | YES | "Send Private Message" link on candidate contact details (needs BuddyPress Messages) |
| Control resume visibility from the profile (xProfile fields)? | NO | The Resume Fields screen was removed |

### Applications, bookmarks and alerts

| Can it... | Status | Delivered by |
|---|---|---|
| Show the member's applications? | YES (needs Applications) | Applied Jobs subnav under Jobs; old `/resumes/applied-jobs/` redirects |
| Notify a job owner when someone applies? | YES (needs Applications) | `applied_job` notification, setting `bpjm_app_notify` |
| Notify a candidate when an application's status changes? | YES (needs Applications) | `application_status` notification, same setting |
| Show saved jobs? | YES (needs Bookmarks) | My Bookmarks subnav |
| Let members manage job alerts in the profile? | YES (needs Alerts) | Job Alerts subnav |

### Community integration

| Can it... | Status | Delivered by |
|---|---|---|
| Post new jobs and resumes to the activity stream? | YES | `bpjm_publish_job_listing` / `bpjm_publish_resume`, each behind its own toggle (off until enabled) |
| Remove activity when a job or resume is trashed or deleted? | YES | `bpjm_delete_job_activity` |
| Show activity text in the reader's language? | YES | `bpjm_format_activity` builds text at render time |
| Add Jobs and Resumes to the admin bar with counts? | YES | `Bp_Job_Manager_Admin::bpjm_setup_admin_bar_links` |
| Work with BuddyBoss Platform? | YES | Accepts `class_exists('BuddyPress')` |
| Style correctly inside Reign and BuddyX? | YES | `reign_load_wp_job_manager_assets`, `.bpjm-screen` wrapper |
| Work with BP Rewrites? | PARTIAL | Compat shim for nav links only, loaded when BP Rewrites is active |

### Site owner

| Can it... | Status | Delivered by |
|---|---|---|
| Configure the plugin in one screen? | YES | General tab, three toggles (job activity, resume activity, application notifications) |
| Rename tabs and subnav labels? | YES | Filters `bpjm_jobs_tab_name`, `bpjm_resume_tab_name`, `bpjm_resumes_tab_link`, `bpjm_resumes_subnav_item_add_resume`, `bpjm_resumes_subnav_item_applied_jobs` |
| Add or remove screens that load the plugin's assets? | YES | Filter `bpjm_owned_profile_actions` |
| Translate it? | YES | POT plus de_DE, es_ES, fr_FR, it_IT, pt_BR shipped |
| Run its own job board (listings, payments, packages)? | NO | WP Job Manager and its add-ons own that |
| Store anything outside one option and two cached counts? | NO | No tables, post types or user meta |

## Known limits

- With no saved settings all three toggles are off; the plugin writes no defaults on activation.
- The two count transients are WPML-suffixed on write but flushed un-suffixed, so a translated copy can show a stale count for up to an hour.
