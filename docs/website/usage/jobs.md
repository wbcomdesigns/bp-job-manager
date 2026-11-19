# Post and manage jobs

Members post and manage their jobs from the **Jobs** tab on their own profile. WP Job Manager draws the forms and lists and decides who may post; this plugin puts them on the profile. Your site needs WP Job Manager's own settings (job fields, moderation, listing duration) configured as usual.

## What a member sees on their own profile

The **Jobs** tab is always on a member's own profile, with these screens:

| Screen | What it does | Needs |
|---|---|---|
| My Jobs | Lists the member's jobs with WP Job Manager's actions (edit, mark filled, delete, and so on) | WP Job Manager |
| Post Job | WP Job Manager's job submission form | WP Job Manager |
| Applied Jobs | The member's own applications and their status | Applications add-on |
| My Bookmarks | Jobs the member saved | Bookmarks add-on |
| Job Alerts | The member's job alerts | Job Alerts add-on |

The count next to **Jobs** shows how many published jobs the member has. A tab with none shows no count.

## Post a job

1. Open your profile and go to **Jobs > Post Job**.
2. Fill in the form and submit it.
3. If your WP Job Manager settings require approval, the job waits as pending until you approve it.
4. Click **Go to My Jobs** to return to the list.

A member who WP Job Manager does not allow to post is sent back to their Jobs tab with the message "You are not authorized to post a job. Please reach out to the administrator for further assistance." This also happens when they open the Post Job address directly. Their Jobs tab then shows their open jobs as job cards instead of the management list.

## Manage a job

In **Jobs > My Jobs**, the actions beside each job come from WP Job Manager. This plugin keeps them on the profile:

- **Edit** opens the job in the profile. Members can edit their own pending and published jobs; this plugin allows both, whatever WP Job Manager's own settings for editing say.
- **Duplicate**, **Continue Submission**, **Relist** and **Renew** open the **Post Job** tab with the job loaded. They work even when WP Job Manager's submission page was never created.
- **Relist** is offered on an expired job.
- **Show applications** (with the Applications add-on) lists the applications for that job. Screens opened this way carry a **Back to My Jobs** link.

Pages of a long list use `?bpjm-page=2` in the address, because BuddyPress does not route `/page/2/` under a member profile. The page size follows WP Job Manager's **Jobs per page** setting. A page past the last one shows "This page is empty" with a link back to the first page.

## What visitors see

- On another member's profile the **Jobs** tab appears only when that member has at least one published job.
- The tab shows the member's open jobs as the same job cards used on your Jobs page, with page links.
- Visitors never see the management list, the Post Job screen or the member's applications.

## Toolbar menu

Members who WP Job Manager lets post get a **Jobs** menu under their BuddyPress account menu in the toolbar, with **My Jobs**, **Applied Jobs** (with the Applications add-on) and **Post Job**, and the job count.

## Limiting who can post

Use WP Job Manager's own settings, or its `job_manager_user_can_post_job` filter. The plugin has no role settings of its own. See [Settings](../settings/index.md#there-are-no-role-settings) and the [Developer Guide](../developer-guide/index.md#permissions).
