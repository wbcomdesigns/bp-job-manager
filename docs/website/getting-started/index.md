---
title: Getting Started
slug: bpjm-getting-started
product: bp-job-manager
order: 1
---

# Getting Started

BuddyPress Job Manager brings WP Job Manager into BuddyPress member profiles. A member who
posts a job gets a **Jobs** tab on their profile; a member who publishes a resume gets a
**Resumes** tab, and their resume appears on that tab.

## What it is

A bridge, not a job board. The plugin stores no jobs and no resumes of its own, and owns no
database tables or post types. Everything lives in WP Job Manager and its add-ons; this
plugin surfaces it inside the community.

## Why it lives on the profile

The profile is already a member's professional identity. You open someone's profile to find
out who they are. If they are looking for work, their resume belongs there. If they are
hiring, the jobs they have posted belong there. The community's social graph becomes the
hiring graph, and nobody leaves their profile to manage either side.

## Who it is for

Communities that want a careers area on member profiles instead of a separate job board: professional and alumni networks, niche industry communities, membership sites. Members who hire post jobs from their profile, and members who look for work keep resumes and track applications there.

## Requirements

| | |
|---|---|
| WordPress | 6.5 or higher |
| PHP | 8.0 or higher |
| BuddyPress | required (BuddyBoss Platform also satisfies the check) |
| WP Job Manager | required, free from wordpress.org |

WordPress itself will not activate this plugin until WP Job Manager is installed and active. If BuddyPress or WP Job Manager is later deactivated, this plugin does nothing, shows an admin notice to administrators and deactivates itself on the next admin page load. Activate the missing plugin, then activate this one again.

The four WP Job Manager add-ons are separate WP Job Manager products and are optional. Each one simply unlocks a tab when you
install it:

| Add-on | What it adds |
|---|---|
| Resume Manager | The Resumes tab: My Resumes and Add Resume |
| Applications | Applied Jobs on the Jobs tab, and notifications to the job author and the applicant |
| Bookmarks | My Bookmarks on the Jobs tab |
| Job Alerts | Job Alerts on the Jobs tab |

## Installing

1. Install and activate **BuddyPress** and **WP Job Manager** first.
2. Install BuddyPress Job Manager and activate it. WordPress sends you to **WB Plugins > Job Manager** once.
3. Open the **General** tab, tick the activity and notification options you want, and click **Save Settings**.

The tabs on member profiles work without step 3. The activity updates and notifications do not: they are all **off** until you save the General tab once, because activation writes no settings. See [Settings](../settings/index.md).

Activation writes nothing except a short-lived flag used to redirect you to the settings
screen once. It creates no pages and changes no member data.

## What happens next

**On their own profile** every member has a Jobs tab with My Jobs and Post Job, plus
(with the add-ons) Applied Jobs, My Bookmarks and Job Alerts. WP Job Manager decides who may post; the
plugin does not add a permission layer of its own.

With Resume Manager active, every member also has a Resumes tab on their own profile with My
Resumes and Add Resume.

**A visitor** sees a member's Jobs tab only if that member actually has jobs, and their
Resumes tab only if they actually have resumes and Resume Manager lets the visitor browse
resumes. Empty tabs, and tabs that would only say "you do not have permission", are not
shown on other people's profiles. Visitors see the jobs and resumes as WP Job Manager's own
listing cards, never the owner's dashboard.

## First job in five minutes

1. Log in as a member and open your own profile.
2. Open **Jobs > Post Job**, fill in the form and submit it.
3. Click **Go to My Jobs** to see the job in your list.
4. Open the same profile logged out. The job shows on the member's Jobs tab as a job card.

For every screen a member sees and what each action does, see [Post and manage jobs](../usage/jobs.md) and [Resumes and applications](../usage/resumes-and-applications.md).

## Related

- [Settings](../settings/index.md)
- [Developer guide](../developer-guide/index.md)
