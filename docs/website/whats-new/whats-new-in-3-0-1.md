# What's New in 3.0.1

Version 3.0.1 makes the Jobs and Resumes tabs work on a normal BuddyPress site, where WP Job Manager's and Resume Manager's own pages are often not set up. It also gives every tab one consistent look. There is nothing to configure after updating.

## For job seekers and employers

- **Applied Jobs moved to the Jobs tab.** Job seekers find their applications under Jobs. The old address under Resumes redirects there, and the tab no longer disappears on sites without Resume Manager.
- **Application status notifications.** When an employer changes the status of an application, the candidate gets a BuddyPress notification that links to Applied Jobs.
- **Back to My Jobs.** After posting a job from the profile, a Go to My Jobs button leads back to the member's jobs. A job's applications, the job edit form and the resume edit form have their own headings and a link back to My Jobs or My Resumes.
- **Duplicate, Continue Submission and Relist work from the profile.** They open the Post Job or Add Resume tab with the job or resume loaded, even when WP Job Manager's submission page is not set. Expired resumes can be relisted.
- **Every page of jobs is reachable.** Members with many jobs get page links again. A page past the last one shows "This page is empty" with a link back to the first page.

## For visitors

- A visitor on a member's Jobs tab sees that member's open jobs as the same job cards used on the site's Jobs page.
- A visitor on a member's Resumes tab sees the member's published resumes as resume cards, following Resume Manager's own setting for who may browse resumes. Visitors who may not browse resumes do not see the Resumes tab at all.
- Visitors no longer see the resume owner's dashboard on someone else's Resumes tab.

## Look and feel

- Every Jobs and Resumes tab shares one set of buttons, tables, notices and empty states, coloured from the active theme in light and dark mode.
- Tables stack into labelled cards on phones, and row buttons are large enough to tap.
- Empty tabs no longer show a "0" count.

## Fixes worth knowing

- The plugin can be activated on BuddyBoss Platform again.
- Logged-out visitors are no longer redirected on every BuddyPress page when WP Job Manager Alerts allows guest alerts.
- Job and resume activity entries show in each reader's language, appear once per job or resume, and keep the job's current title.
- The "applied for your job" notification opens that job's applications.

## For developers

- The `bpjm_load_more_jobs` AJAX action, its script and the plugin's own job table template are removed. Job lists use WP Job Manager's pagination with a `bpjm-page` query argument. If a theme or custom code relied on either, update it.

See the full list in the plugin changelog.
