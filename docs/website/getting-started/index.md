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

## Requirements

| | |
|---|---|
| WordPress | 6.5 or higher |
| PHP | 8.0 or higher |
| BuddyPress | required |
| WP Job Manager | required |

The four WP Job Manager add-ons are optional. Each one simply unlocks a tab when you
install it:

| Add-on | What it adds |
|---|---|
| Resume Manager | The Resumes tab: My Resumes, Add Resume, Applied Jobs |
| Applications | The Applied Jobs list and a notification to the job author |
| Bookmarks | Saved jobs on the Jobs tab |
| Job Alerts | Job alerts on the Jobs tab |

## Installing

1. Install and activate **BuddyPress** and **WP Job Manager** first.
2. Install BuddyPress Job Manager and activate it.
3. That is the whole setup. There is nothing to configure.

Activation writes nothing except a short-lived flag used to redirect you to the settings
screen once. It creates no pages and changes no member data.

## What happens next

**A member who posts a job** gets a Jobs tab on their profile containing My Jobs, Post Job,
and (with the add-ons) Saved jobs and Job alerts. WP Job Manager decides who may post; the
plugin does not add a permission layer of its own.

**A member who publishes a resume** gets a Resumes tab containing My Resumes, Add Resume
and Applied Jobs.

**A visitor** sees a member's Jobs tab only if that member actually has jobs, and their
Resumes tab only if they actually have resumes. Empty tabs are not shown on other people's
profiles.

## Related

- [Settings](../settings/index.md)
- [Developer guide](../developer-guide/index.md)
