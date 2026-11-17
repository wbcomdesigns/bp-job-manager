---
title: Troubleshooting
slug: bpjm-troubleshooting
product: bp-job-manager
order: 5
---

# Troubleshooting

## The plugin says it is "ineffective now"

The admin notice reads "BuddyPress Job Manager is ineffective now as it requires BuddyPress and WP Job Manager to be installed and active." One of the two is missing or switched off. Activate it and the tabs come back. No data is lost, because jobs and resumes live in WP Job Manager.

## A tab or screen is missing

- **Resumes tab, Applied Jobs, My Bookmarks or Job Alerts** each need their WP Job Manager add-on. See the add-on table in [Getting Started](../getting-started/index.md).
- **On someone else's profile**, empty tabs are hidden on purpose. See the [FAQ](../faq/index.md).

## "You are not authorized to post a job"

A member who opens Post Job but is not allowed to post is sent back to their Jobs tab with this message. WP Job Manager made that decision. Check its settings for who may post jobs, for example whether an account is required.

## Page 2 of a list shows "Page not found"

BuddyPress returns a 404 for `/page/2/` under a member's profile, so the plugin pages its lists with `?bpjm-page=2` instead. The built-in pagination links already use it. If you added your own links to a page of jobs or resumes on a profile, build them the same way.

## Job or resume screens look different from the rest of the theme

Every Jobs and Resumes screen is wrapped in `.bpjm-screen`, and the plugin's stylesheet styles WP Job Manager's markup inside it with your theme's colours. If a theme rule still wins, target `.bpjm-screen` in your custom CSS rather than editing WP Job Manager templates.
