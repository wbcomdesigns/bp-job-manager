# What's New in 3.0.1

Version 3.0.1 makes the Jobs and Resumes tabs work on a normal BuddyPress site, where WP Job Manager's and Resume Manager's own pages are often not set up. It also gives every tab one consistent look and adds a notification for candidates. Read the update notes and run the short retest below before you tell your members the update is done.

## Update notes

- **Nothing to reconfigure, and nothing is turned on for you.** The settings keep their saved values. On a site that never saved the General tab, the activity and notification options are still off. See [Settings](../settings/index.md#general).
- **Application status notifications use the existing setting.** Candidates are notified of a status change only when **Application notifications** is on. If you turned that on for employers, candidates now get notifications too. It is one setting for both.
- **Applied Jobs moved from the Resumes tab to the Jobs tab.** The old address redirects. Update any menu link or custom link that points at the Resumes address.
- **Custom code may need an update.** The `bpjm_load_more_jobs` AJAX action, its script and the plugin's own job table template are removed. If a theme styled the old job table or relied on that action, change it. See [For developers](#for-developers).
- **Requirements:** WordPress 6.5 or later, PHP 8.0 or later, BuddyPress and WP Job Manager. The release was tested with Reign 8.0.9, BuddyX 5.2.0, BuddyX Pro 5.1.8 and Twenty Twenty-Five.

## For job seekers and employers

- **Application status notifications.** When an employer changes the status of an application, the candidate gets a BuddyPress notification that links to Applied Jobs.
- **Back to My Jobs.** After posting a job from the profile, a Go to My Jobs button leads back to the member's jobs. A job's applications, the job edit form and the resume edit form have their own headings and a link back to My Jobs or My Resumes.
- **Duplicate, Continue Submission and Relist work from the profile.** They open the Post Job or Add Resume tab with the job or resume loaded, even when WP Job Manager's submission page is not set. Expired resumes can be relisted.
- **Every page of jobs is reachable.** Members with many jobs get page links again. A page past the last one shows "This page is empty" with a link back to the first page.
- **Saving an edited resume stays on the profile** when Resume Manager's Candidate Dashboard page is set.
- **Job and resume activity** shows in each reader's language with the job's current title, appears once per job or resume, and appears in the activity filter.
- **Empty job description.** Clicking Preview with an empty description no longer reveals a second, raw text box.

## For visitors

- A visitor on a member's Jobs tab sees that member's open jobs as the same job cards used on the site's Jobs page.
- A visitor on a member's Resumes tab sees the member's published resumes as resume cards, following Resume Manager's own setting for who may browse resumes. Visitors who may not browse resumes do not see the Resumes tab at all.
- Visitors no longer see the resume owner's dashboard on someone else's Resumes tab.

## Look and feel

- Every Jobs and Resumes tab shares one set of buttons, tables, notices and empty states, coloured from the active theme in light and dark mode.
- Job and resume notices (saved, errors, required fields) use BuddyPress's own notice style, so they match the other BuddyPress add-ons on the profile.
- Tables stack into labelled cards on phones, and row buttons are large enough to tap.
- Column labels, job status, field hints and "(optional)" keep readable contrast on every theme.
- Empty tabs no longer show a "0" count, and the toolbar's Resumes menu only appears when Resume Manager is active.

## Fixes worth knowing

- The plugin can be activated on BuddyBoss Platform again.
- Logged-out visitors are no longer redirected on every BuddyPress page when WP Job Manager Alerts allows guest alerts.
- The "applied for your job" notification opens that job's applications and is marked read.
- A corrupted plugin settings row no longer takes the whole site down.
- The plugin's script loads only on the Jobs and Resumes tabs, and its stylesheet no longer loads on WP Job Manager's own Job Dashboard page.

## Retest after updating

1. Open your own profile. Check the Jobs tab: My Jobs, Post Job, and Applied Jobs if the Applications add-on is active.
2. Post a test job, then click **Go to My Jobs**.
3. On a job that has expired, use **Relist** and confirm it opens Post Job with the job loaded.
4. With Resume Manager, add a resume, then use **Relist** on an expired one.
5. Log out and open a member's profile. Confirm the Jobs and Resumes tabs show cards, and that a member with no published jobs has no Jobs tab.
6. Open **WB Plugins > Job Manager > General** and confirm the options match what you want. Turn on **Application notifications**, have a test candidate apply, then change the application's status and check the candidate's notification.
7. With Resume Manager active, open `/resumes/applied-jobs/` on your profile and confirm it redirects to Applied Jobs.
8. Check the tabs on a phone-width screen and in dark mode.

## For developers

- The `bpjm_load_more_jobs` AJAX action, its script and the plugin's own job table template are removed. Job lists use WP Job Manager's pagination with a `bpjm-page` query argument. If a theme or custom code relied on either, update it.
- The new notification uses the BuddyPress component `bpjm_notify_actions` with the action `application_status`. The text of the employer notification can still be changed with the `_bprwg_notification_text` filter.
- Hooks removed in 3.0.0 stay removed. See the [Developer Guide](../developer-guide/index.md#hooks-removed-in-300).
