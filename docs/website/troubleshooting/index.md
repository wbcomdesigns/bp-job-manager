---
title: Troubleshooting
slug: bpjm-troubleshooting
product: bp-job-manager
order: 5
---

# Troubleshooting

## The plugin says it is "ineffective now"

The admin notice reads "BuddyPress Job Manager is ineffective now as it requires BuddyPress and WP Job Manager to be installed and active." One of the two is missing or switched off, and the plugin deactivates itself on the next admin page load. Activate the missing plugin, then activate this one again, and the tabs come back. No data is lost, because jobs and resumes live in WP Job Manager.

## No activity updates after members post jobs

The **Post job activity** option is off until you save it. Open **WB Plugins > Job Manager > General**, tick it and click **Save Settings**. The **Overview** tab shows "Off" for any option that is not on. Jobs posted before you turned it on get no update.

## Application notifications do not arrive

Check each of these:

1. **Application notifications** is ticked and saved on the General tab. It only appears when the Applications add-on is active.
2. The BuddyPress **Notifications** component is active under **Settings > BuddyPress > Components**.
3. The application was made after the option was turned on.
4. For the candidate, the employer changed the status to something other than archived. Archiving never notifies.

## Resume activity does not appear

**Post resume activity** only exists when Resume Manager is active, and it is off until saved. Resumes published before you turned it on get no update.

## A tab or screen is missing

- **Resumes tab, Applied Jobs, My Bookmarks or Job Alerts** each need their WP Job Manager add-on. See the add-on table in [Getting Started](../getting-started/index.md).
- **On someone else's profile**, empty tabs are hidden on purpose, and the count only includes published jobs and resumes. See the [FAQ](../faq/index.md).
- **Applied Jobs** is on the Jobs tab, not the Resumes tab. The old Resumes address redirects there.

## "You are not authorized to post a job"

A member who opens Post Job but is not allowed to post is sent back to their Jobs tab with this message. WP Job Manager made that decision. Check its settings for who may post jobs, for example whether an account is required.

## Page 2 of a list shows "Page not found"

BuddyPress returns a 404 for `/page/2/` under a member's profile, so the plugin pages its lists with `?bpjm-page=2` instead. The built-in pagination links already use it. If you added your own links to a page of jobs or resumes on a profile, build them the same way.

## Job or resume screens look different from the rest of the theme

Every Jobs and Resumes screen is wrapped in `.bpjm-screen`, and the plugin's stylesheet styles WP Job Manager's markup inside it with your theme's colours. If a theme rule still wins, target `.bpjm-screen` in your custom CSS rather than editing WP Job Manager templates.
