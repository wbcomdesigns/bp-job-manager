---
title: Developer Guide
slug: bpjm-developer-guide
product: bp-job-manager
order: 3
---

# Developer Guide

## Architecture

A bridge. WordPress-Plugin-Boilerplate layout: bootstrap → loader → globals → admin/public.
No PHP namespace, no service container, **no database tables, no post types, no REST
routes**. It operates entirely on WP Job Manager's `job_listing`, `resume` and
`job_application` posts and on BuddyPress activity and notifications.

| | |
|---|---|
| Main file | `bp-job-manager.php` |
| Class prefix | `Bp_Job_Manager_*` |
| Text domain | `bp-job-manager` (equals the wordpress.org slug) |
| Option | `bpjm_general_settings` |
| AJAX | none (the `bpjm_load_more_jobs` infinite scroll was removed in 3.0.1) |
| Templates | `public/templates/bpjm-job-application.php` (legacy Apply page only) |
| Pagination | WP Job Manager's pager, pointed at `?bpjm-page=N` (BuddyPress does not route `/page/N/` under a member URL) |
| Screen wrapper | every Jobs / Resumes screen renders inside `.bpjm-screen`; `public/css/bp-job-manager-public.css` styles the host markup there and nowhere else |

### Three rules

Most of this plugin's historical bugs came from breaking one of these.

1. **Never rebuild what the host plugin already renders.** Use its shortcode. A re-render
   drifts from the original, and it duplicates strings into our text domain that the host
   already has community translations for, so a translated site shows our tab in English.
2. **Never store what can be derived at render time.** Per-user state written at activation
   only ever covers users who existed then; everyone who registers later silently misses it.
3. **Never add a permission system beside WP Job Manager's.** Defer to its capability.

### Which screens are ours

| Screen | Rendered by |
|---|---|
| Jobs → My Jobs | `[job_dashboard]` |
| Jobs → Post Job | `[submit_job_form]` |
| Jobs → Applied Jobs | `[past_applications]` |
| Jobs → My Bookmarks | `[my_bookmarks]` |
| Jobs → Job Alerts | `[job_alerts]` |
| Resumes → My Resumes | `[candidate_dashboard]` |
| Resumes → Add Resume | `[submit_resume_form]` |
| Apply To Job page | `[job_apply]`, in our template |

Eight screens, eight host shortcodes. We own the tab, the frame and the context; the host
owns the markup. If you find yourself writing a table that a host plugin already renders,
stop and use its shortcode.

## Hooks

Every hook below is verified as actually fired in 3.0.0.

### Filters

| Hook | Args | Purpose |
|---|---|---|
| `bpjm_admin_tabs` | `array $tabs` | Add, remove or reorder admin tabs |
| `bpjm_menu_page_capability` | `string $capability` | Capability required for the admin page |
| `bpjm_jobs_tab_name` | `string $label` | Label of the Jobs subnav item |
| `bpjm_resume_tab_name` | `string $label` | Label of the Resumes subnav item |
| `bpjm_resumes_tab_link` | `string $link` | Base URL of the Resumes tab |
| `bpjm_resumes_subnav_item_add_resume` | `string $label` | "Add Resume" subnav label |
| `bpjm_resumes_subnav_item_applied_jobs` | `string $label` | "Applied Jobs" subnav label |
| `bpjm_owned_profile_actions` | `string[] $actions` | Which `bp_current_action()` values count as our screens, for asset loading |
| `_bprwg_notification_text` | `string $text` | Application notification text. Legacy name, kept for backwards compatibility |

### Screen labels come from one accessor

`Bp_Job_Manager_Public::bpjm_get_member_screen_label( $screen, $user_id )` produces the nav
label and the screen heading for both Jobs and Resumes, so the tab a member clicks and the
heading they land on can never disagree. Filter `bpjm_jobs_tab_name` /
`bpjm_resume_tab_name` rather than re-inlining the strings.

### Permissions

```php
bpjm_user_can_post_job( $user = null );   // defers to job_manager_user_can_post_job
bpjm_user_can_apply_job( $user = null );  // any logged-in member
```

To restrict posting, filter WP Job Manager's own hook:

```php
add_filter( 'job_manager_user_can_post_job', function ( $can_post ) {
	return current_user_can( 'edit_others_posts' );
} );
```

Do **not** re-add a role list to this plugin. It had one until 3.0.0 and it was the direct
cause of members being shown an employer dashboard they were then refused.

### Add-on detection

```php
bpjm_is_plugin_active( 'wp-job-manager-resumes/wp-job-manager-resumes.php' );
```

Always use this rather than reading `active_plugins` directly: a bare `in_array()` against
that option reports every network-activated add-on as inactive on every multisite subsite.
Core's `is_plugin_active()` does the same job but is admin-only, and most of these checks
run on the front end.

### Icons

```php
echo bpjm_icon( 'briefcase', array( 'size' => 20, 'class' => 'my-class' ) );
```

Inline Lucide SVG, 24×24 stroke geometry, `currentColor`. Twelve icons are registered in
`includes/admin/class-bp-job-manager-icons.php`.

The split is by owner, not by preference: **WordPress chrome uses Dashicons, anything we
render uses Lucide.** So the top-level admin menu icon stays `dashicons-lightbulb` - that
argument is an icon URL, and a Dashicons class is what follows the admin colour scheme
alongside every other menu. Inside our own cards, tabs and panels, use `bpjm_icon()`.

## Hooks removed in 3.0.0

These no longer fire. A site filtering any of them needs to know.

| Removed hook | Why |
|---|---|
| `bpjm_profile_peronal_inf_txt` | The resume is no longer duplicated onto the profile screen |
| `bpjm_profile_urls_txt` | Same |
| `bpjm_profile_education_txt` | Same |
| `bpjm_profile_experience_txt` | Same |
| `bpjm_resume_fields_custom_text` | The Youzify-only resume-fields menu is gone |
| `bpjm_resume_fields_custom_slug` | Same |
| `bpjm_applied_jobs_per_page` | Applied Jobs is rendered by `[past_applications]`; use `job_manager_job_applications_past_args` |

We also stopped filtering `job_manager_user_can_post_job`. Do not re-add a callback there
that calls `bpjm_user_can_post_job()`, and that function asks WP Job Manager through the same
hook, so it would recurse until the request exhausts memory.

## Quality gates

Run `composer install` first; both gates need dev dependencies.

```bash
composer phpstan   # level 5, baseline is ZERO errors
composer phpcs     # baseline 38 errors / 8 warnings, all pre-existing
composer i18n      # make-pot -> make-mo -> make-php
```

`make-pot` runs WP-CLI's i18n audit. Keep it at zero warnings rather than re-adding
`--skip-audit`.

### Translation

The bar is translation-**ready**, not translated. To prove it, build a pseudo-locale from
the POT where every `msgstr` is `«msgid»`, compile it, switch to it and read every screen:
anything still plain English is either unwrapped or third-party.

Two traps that make this look broken when it is not: in wp-admin `determine_locale()`
returns the **user's** profile language rather than the site's, so set the test user's
locale too; and BuddyPress / WP Job Manager strings on our screens belong to them. Product
names (`BuddyPress`, `WP Job Manager`, `Resume Manager`, `Applications`) are deliberately
never translated.

## Related

- [Getting started](../getting-started/index.md)
- [Settings](../settings/index.md)
- `audit/manifest.json`: machine-readable inventory
