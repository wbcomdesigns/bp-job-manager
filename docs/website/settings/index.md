---
title: Settings
slug: bpjm-settings
product: bp-job-manager
order: 2
---

# Settings

**WB Plugins → Job Manager.** Four screens, and only one of them has anything to change.

## General

Three toggles, all on by default. Nothing here is required for the plugin to work.

| Setting | What it does |
|---|---|
| Post job activity | Adds a BuddyPress activity update when a member posts a job |
| Post resume activity | Adds a BuddyPress activity update when a member publishes a resume |
| Application notifications | Sends the job author a BuddyPress notification when someone applies, and the applicant one when the job author changes the application's status |

The resume and application rows only appear when the matching add-on is active.

### There are no role settings

Earlier versions asked you to choose which member roles could post jobs and which could
apply. Those settings are gone as of 3.0.0.

WP Job Manager already decides who may post a job, and this plugin now defers to it rather
than running a second permission system alongside it. If you want to narrow who can post,
filter `job_manager_user_can_post_job`. That is WP Job Manager's own contract, and it
applies everywhere on the site rather than only on the profile tabs.

## Overview

Read-only. It reports which integrations are active and what the current configuration
actually does. If a feature is on but reaching nobody, this screen says so rather than
reporting a cheerful "On".

## Help & Support

Links to the documentation and the support centre.

## Discover

Other free Wbcom Designs community plugins. Read-only.

## What the plugin stores

Apart from its settings, nothing.

| Stored | Where | Why |
|---|---|---|
| `bpjm_general_settings` | option | The three toggles above |
| `bp-job-manager_activation_date`, `bp-job-manager_no_bug` | options | When the plugin was installed, and whether the review notice was dismissed |
| `bpjm_user_{ID}_job_listings`, `bpjm_user_{ID}_my_resumes_count` | transients | A cache for the profile nav counts, cleared whenever a job or resume changes status |

That is the whole list. It creates no pages, writes no per-member data, and owns no tables
or post types.

Uninstalling removes every row above. It does not touch jobs, resumes or applications:
those belong to WP Job Manager and its add-ons and stay exactly as they are. If your site
still has the "Apply To Job" page that versions before 3.0.0 created, that page is yours
now and is left in place; only the plugin's pointer to it is removed.

## Related

- [Getting started](../getting-started/index.md)
- [Developer guide](../developer-guide/index.md)
