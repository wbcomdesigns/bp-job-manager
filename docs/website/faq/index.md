---
title: FAQ
slug: bpjm-faq
product: bp-job-manager
order: 4
---

# FAQ

## Is there anything to set up?

The profile tabs need nothing: activate BuddyPress, WP Job Manager and this plugin, and the Jobs tab appears on every member's own profile. The activity updates and application notifications are off until you tick them under **WB Plugins > Job Manager > General** and click **Save Settings**. See [Settings](../settings/index.md).

## Why do no activity updates or notifications appear?

Because the three options on the General tab are off until you save them once. Open **WB Plugins > Job Manager > General**, tick what you want and click **Save Settings**. Resume activity needs Resume Manager, and application notifications need the Applications add-on and the BuddyPress Notifications component. Only jobs, resumes and applications from that point on are covered. See [Activity and notifications](../usage/activity-and-notifications.md).

## Which WP Job Manager add-ons do I need?

None. WP Job Manager is the only requirement. Resume Manager adds the Resumes tab, the Applications add-on adds Applied Jobs and notifications, Bookmarks adds My Bookmarks and Job Alerts adds Job Alerts. They are separate WP Job Manager products.

## How does a member apply for a job?

On the job's own page, with the apply form of the Applications add-on. This plugin adds Applied Jobs on the member's profile to follow the applications, and the notifications. See [Resumes and applications](../usage/resumes-and-applications.md).

## Does it work with BuddyBoss, BuddyX and Reign?

It checks for the BuddyPress class, which BuddyBoss Platform also provides, so it activates there. The 3.0.1 release was tested with Reign 8.0.9, BuddyX 5.2.0, BuddyX Pro 5.1.8 and Twenty Twenty-Five. Tabs, buttons and notices use your theme's colours, in light and dark mode.

## Who can post a job?

WP Job Manager decides, not this plugin. Whoever WP Job Manager allows to post can use Post Job on their profile. There are no role settings here, so to narrow who can post, change WP Job Manager's own settings.

## Do I need WP Job Manager's Job Dashboard and Submit Job pages?

No. On a community site the profile tabs are those screens. Duplicate, Continue, Relist and Renew all bring the member back to their own Post Job tab, and Resume Manager's Relist brings them back to Add Resume, even when those WP Job Manager pages were never created.

## Why can't I see another member's Jobs or Resumes tab?

On someone else's profile a tab only appears when there is something in it. The Jobs tab needs at least one job. The Resumes tab needs at least one resume, and Resume Manager has to allow you to browse resumes. Your own profile always shows your tabs so you can start.

## Where is Applied Jobs?

On the Jobs tab, when the Applications add-on is active. It moved there from the Resumes tab in 3.0.1, so it now works without Resume Manager. Old links to the Resumes tab's Applied Jobs redirect automatically.

## Who gets notified about applications?

With **Application notifications** on and the Applications add-on active, the job author is notified when someone applies, and the applicant is notified when the job author changes the application's status. Each notification opens the matching screen on the member's profile.

## Is the plugin translatable?

Yes. Its own text uses the `bp-job-manager` text domain. Job forms, dashboards and listing cards are drawn by WP Job Manager and its add-ons, so their wording comes from those plugins' translations.
