---
title: Settings
slug: bpjm-settings
product: bp-job-manager
order: 2
---

# Settings

**WB Plugins → Job Manager.** Four screens, and only one of them has anything to change.

## General

Four toggles, all on by default. Nothing here is required for the plugin to work.

| Setting | What it does |
|---|---|
| Post job activity | Adds a BuddyPress activity update when a member posts a job |
| Post resume activity | Adds a BuddyPress activity update when a member publishes a resume |
| Application notifications | Sends the job author a BuddyPress notification when someone applies |

The resume and application rows only appear when the matching add-on is active.

### There are no role settings

Earlier versions asked you to choose which member roles could post jobs and which could
apply. Those settings are gone as of 3.0.0.

WP Job Manager already decides who may post a job, and this plugin now defers to it rather
than running a second permission system alongside it. If you want to narrow who can post,
filter `job_manager_user_can_post_job` — that is WP Job Manager's own contract, and it
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
| `bpjm_general_settings` | option | The four toggles above |
| Job and resume counts | transients | A cache for the profile nav badges, regenerated on demand |

It creates no pages, writes no per-member data, and owns no tables or post types. Jobs,
resumes and applications all belong to WP Job Manager and its add-ons; uninstalling this
plugin does not touch them.

## Related

- [Getting started](../getting-started/index.md)
- [Developer guide](../developer-guide/index.md)
