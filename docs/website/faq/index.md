# Frequently Asked Questions

## What are the plugin requirements?

Two plugins, and only two: [BuddyPress](https://buddypress.org/) (12.0 or higher) and
[WP Job Manager](https://wpjobmanager.com/). If either is missing, the plugin shows an admin
notice and stays inactive. WordPress 6.5+ and PHP 8.0+ are also required.

## Which WP Job Manager add-ons do I need?

None of them. Every add-on is optional, and each one unlocks an extra part of the plugin when
you install it:

- **Resume Manager** unlocks the Resumes tab: My Resumes and Add Resume.
- **Applications** unlocks the Applied Jobs tab (shown under the Resumes tab) and the
  BuddyPress notification to the job author when someone applies.
- **Bookmarks** unlocks the My Bookmarks tab on the profile.
- **Job Alerts** unlocks the Job Alerts tab on the profile.

Without any add-ons, the plugin still gives you the Jobs tab, the My Jobs dashboard,
in-profile job posting, admin bar menus, and job activity in the stream.

## Where does a member's resume appear?

On their Resumes tab, which is where Resume Manager's own dashboard renders it. Until 3.0.0
the plugin also printed a copy of the resume onto the profile screen itself, with a setting
and nine per-member visibility checkboxes. That duplicate is gone; no resume is affected.

## How do I control who can post jobs?

Through WP Job Manager, which already decides this for the whole site. This plugin keeps no
second list of roles and creates no roles of its own. To narrow posting, filter
`job_manager_user_can_post_job`. Any logged-in member can apply for a job, which is WP Job
Manager's behaviour on the listing, and the profile now matches it.

## Does the job author get notified when someone applies?

Yes, when the Applications add-on is active. The job author receives a BuddyPress
notification for each application. You can turn it off under **Application notifications** in
the General settings.

## Are jobs and resumes added to the activity stream?

Only if you enable it. New jobs and new resumes each have their own toggle in the settings.
When a job or resume is later trashed or deleted, its activity entry is removed
automatically, so members never click through to a listing that no longer exists.

## Can members message a candidate?

Yes. When the BuddyPress Messages component is active, candidate contact details include a
**Send Private Message** link that opens a BuddyPress private message to that candidate. With
Messages inactive, the link is not shown.

## Will this work with my theme?

The plugin targets plain BuddyPress and follows BuddyPress template standards, so it works
with any properly coded BuddyPress-compatible theme. It is tested with BuddyX and Reign, and
the WP Job Manager surfaces are styled for dark mode in BuddyX, BuddyX Pro and Reign.
BP Rewrites is supported (detected at runtime).

## Is the plugin translation ready?

Yes. It ships a POT file and is translation ready. Note that product names (BuddyPress,
WP Job Manager, Resume Manager, Applications) are deliberately never translated, and the
screens rendered by WP Job Manager and its add-ons carry their translations, not this
plugin's.

## Does the plugin create any pages or database tables?

No. It owns no tables, no post types and no REST routes, and it creates no pages. It stores
only its settings option and a small transient cache for the profile nav counts. Sites
upgraded from before 3.0.0 may still have an old "Apply To Job" page; that page is left in
place and only the plugin's pointer to it is removed.

## Related

- [Getting started](../getting-started/index.md)
- [Usage](../usage/index.md)
- [Troubleshooting](../troubleshooting/index.md)
